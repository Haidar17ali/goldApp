<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\AccountingHelper;
use App\Models\Journal;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['branch', 'details'])
            ->latest();

        // 🔍 Filter by branch (optional)
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // 🔍 Filter tanggal (optional)
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $expenses = $query->paginate(10);
        $branches = Branch::all();

        return view('pages.expenses.index', compact('expenses', 'branches'));
    }


    public function create()
    {
        $branches = Branch::all();

        return view('pages.expenses.create', compact('branches'));
    }

    private function getBranchExpenseAccount($branchId)
    {
        return match ($branchId) {
            2 => '502.03.01', // Paserpan
            1 => '502.03.02', // Pasuruan
            3 => '502.03.03', // SA
            default => '502.03.00',
        };
    }

    private function handleJournal($expense, $totalAmount)
    {
        $journal = \App\Models\Journal::with('items.account')
            ->where('source_type', 'expense')
            ->where('source_id', $expense->id)
            ->first();

        $expenseAccount = $this->getBranchExpenseAccount($expense->branch_id);
        $cashAccount = '101.00.01';

        // 🆕 CREATE
        if (!$journal) {

            \App\Helpers\AccountingHelper::post([
                'date' => $expense->date,
                'reference' => $expense->code,
                'description' => 'Pengeluaran ' . $expense->code,
                'source_type' => 'expense',
                'source_id' => $expense->id,
                'branch_id' => $expense->branch_id,
                'lines' => [
                    [
                        'account' => $expenseAccount,
                        'debit' => $totalAmount,
                        'credit' => 0,
                    ],
                    [
                        'account' => $cashAccount,
                        'debit' => 0,
                        'credit' => $totalAmount,
                    ]
                ]
            ]);
        } else {

            // 🔄 UPDATE EXISTING
            foreach ($journal->items as $item) {

                $code = $item->account->code;

                if ($code === $expenseAccount) {
                    $item->increment('debit', $totalAmount);
                }

                if ($code === $cashAccount) {
                    $item->increment('credit', $totalAmount);
                }
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $user = auth()->user();
            $branchId = $user->profile->branch_id;

            // 🔍 1. Cari expense (per tanggal + cabang)
            $expense = Expense::where('branch_id', $branchId)
                ->whereDate('date', $request->date)
                ->first();

            // 🆕 2. Kalau belum ada → buat
            if (!$expense) {
                $expense = Expense::create([
                    'code' => 'EXP-' . now()->format('YmdHis'),
                    'branch_id' => $branchId,
                    'date' => $request->date,
                    'description' => 'Pengeluaran harian',
                    'total_amount' => 0,
                    'created_by' => $user->id
                ]);
            }

            $totalInput = 0;

            // 🔁 3. Simpan semua detail
            foreach ($request->items as $item) {

                $expense->details()->create([
                    'item_name' => $item['item_name'],
                    'amount' => $item['amount'],
                    'note' => $item['note'] ?? null
                ]);

                $totalInput += $item['amount'];
            }

            // ➕ 4. Update total
            $expense->increment('total_amount', $totalInput);

            // 📘 5. Update / Create Journal
            $this->handleJournal($expense, $totalInput);
        });

        return redirect()
            ->route('pengeluaran-toko.index')
            ->with('success', 'Pengeluaran berhasil disimpan');
    }

    public function show($id)
    {
        $expense = Expense::with(['branch', 'details'])
            ->findOrFail($id);

        // ambil journal terkait
        $journal = Journal::with(['items.account'])
            ->where('source_type', 'expense')
            ->where('source_id', $expense->id)
            ->first();

        return view('pages.expenses.show', compact('expense', 'journal'));
    }

    public function edit($id)
    {
        $expense = Expense::with('details')->findOrFail($id);

        return view('pages.expenses.edit', compact('expense'));
    }

    private function handleJournalNew($expense, $amount)
    {
        $expenseAccount = $this->getBranchExpenseAccount($expense->branch_id);
        $cashAccount = '101.00.01';

        \App\Helpers\AccountingHelper::post([
            'date' => $expense->date,
            'reference' => $expense->code,
            'description' => 'Update Pengeluaran ' . $expense->code,
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'branch_id' => $expense->branch_id,
            'lines' => [
                [
                    'account' => $expenseAccount,
                    'debit' => $amount,
                    'credit' => 0,
                ],
                [
                    'account' => $cashAccount,
                    'debit' => 0,
                    'credit' => $amount,
                ]
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            $expense = Expense::with('details')->findOrFail($id);

            // 🔥 1. REVERSE JOURNAL LAMA
            $journal = Journal::with('items')
                ->where('source_type', 'expense')
                ->where('source_id', $expense->id)
                ->whereNull('reversal_of')
                ->first();

            if ($journal) {
                \App\Helpers\AccountingHelper::reverse($journal, 'Edit Expense');
            }

            // 🔥 2. HAPUS DETAIL LAMA
            $expense->details()->delete();

            // 🔥 3. UPDATE HEADER
            $expense->update([
                'date' => $request->date,
                'total_amount' => 0 // reset dulu
            ]);

            $total = 0;

            // 🔥 4. INSERT DETAIL BARU
            foreach ($request->items as $item) {

                $expense->details()->create([
                    'item_name' => $item['item_name'],
                    'amount' => $item['amount'],
                    'note' => $item['note'] ?? null
                ]);

                $total += $item['amount'];
            }

            // 🔥 5. UPDATE TOTAL
            $expense->update([
                'total_amount' => $total
            ]);

            // 🔥 6. POST JOURNAL BARU
            $this->handleJournalNew($expense, $total);
        });

        return redirect()
            ->route('pengeluaran-toko.index')
            ->with('success', 'Pengeluaran berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $expense = Expense::with('details')->findOrFail($id);

            // 🔥 Ambil jurnal utama (bukan reversal)
            $journal = Journal::where('source_type', 'expense')
                ->where('source_id', $expense->id)
                ->whereNull('reversal_of')
                ->whereDoesntHave('reversedBy')
                ->first();

            if ($journal) {
                AccountingHelper::reverse($journal, 'Hapus Expense');
            }

            // 🔥 Reverse jurnal (kalau belum pernah direverse)
            if ($journal && !$journal->reversedBy) {
                AccountingHelper::reverse($journal, 'Hapus Payroll');
            }

            // 🔥 3. SOFT DELETE EXPENSE
            $expense->delete();
        });

        return redirect()
            ->route('pengeluaran-toko.index')
            ->with('success', 'Pengeluaran berhasil dihapus (reversal dilakukan)');
    }
}
