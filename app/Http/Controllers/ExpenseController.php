<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\AccountingHelper;
use App\Models\Journal;

class ExpenseController extends BaseController
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
        $bankAccounts = BankAccount::where('is_active', true)->get();


        return view('pages.expenses.create', compact('branches', 'bankAccounts'));
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

    // private function handleJournal($expense, $totalAmount)
    // {
    //     $journal = \App\Models\Journal::with('items.account')
    //         ->where('source_type', 'expense')
    //         ->where('source_id', $expense->id)
    //         ->first();

    //     $expenseAccount = $this->getBranchExpenseAccount($expense->branch_id);
    //     $cashAccount = '101.00.01';

    //     // 🆕 CREATE
    //     if (!$journal) {

    //         \App\Helpers\AccountingHelper::post([
    //             'date' => $expense->date,
    //             'reference' => $expense->code,
    //             'description' => 'Pengeluaran ' . $expense->code,
    //             'source_type' => 'expense',
    //             'source_id' => $expense->id,
    //             'branch_id' => $expense->branch_id,
    //             'lines' => [
    //                 [
    //                     'account' => $expenseAccount,
    //                     'debit' => $totalAmount,
    //                     'credit' => 0,
    //                 ],
    //                 [
    //                     'account' => $cashAccount,
    //                     'debit' => 0,
    //                     'credit' => $totalAmount,
    //                 ]
    //             ]
    //         ]);
    //     } else {

    //         // 🔄 UPDATE EXISTING
    //         foreach ($journal->items as $item) {

    //             $code = $item->account->code;

    //             if ($code === $expenseAccount) {
    //                 $item->increment('debit', $totalAmount);
    //             }

    //             if ($code === $cashAccount) {
    //                 $item->increment('credit', $totalAmount);
    //             }
    //         }
    //     }
    // }

    private function handleJournal($expense)
    {
        $journal = \App\Models\Journal::where('source_type', 'expense')
            ->where('source_id', $expense->id)
            ->first();

        if ($journal) {
            $journal->items()->delete();
            $journal->delete();
        }

        $expenseAccount = $this->getBranchExpenseAccount($expense->branch_id);

        $lines = [];

        $totalExpense = 0;

        foreach ($expense->details as $detail) {

            $amount = $detail->amount;

            $totalExpense += $amount;

            // =========================
            // CREDIT ACCOUNT
            // =========================

            if ($detail->payment_type === 'cash') {
                $cashAccounts = [
                    1 => '101.00.01',   // Pasuruan\
                    2 => '101.00.011',  // Sandang Ayu
                ];

                $creditAccount = $cashAccounts[$expense->branch_id] ?? '101.00.00';
            } else {

                $bank = \App\Models\BankAccount::find($detail->bank_account_id);

                if (!$bank) {
                    throw new \Exception('Bank account tidak ditemukan');
                }

                $creditAccount = $bank->account_code;
            }

            // CREDIT
            $lines[] = [
                'account' => $creditAccount,
                'debit' => 0,
                'credit' => $amount,
            ];
        }

        // DEBIT EXPENSE
        $lines[] = [
            'account' => $expenseAccount,
            'debit' => $totalExpense,
            'credit' => 0,
        ];

        \App\Helpers\AccountingHelper::post([
            'date' => $expense->date,
            'reference' => $expense->code,
            'description' => 'Pengeluaran ' . $expense->code,
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'branch_id' => $expense->branch_id,
            'lines' => $lines
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:1',
            'items.*.payment_type' => 'required',
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

                $paymentType = 'cash';
                $bankId = null;

                if ($item['payment_type'] != "cash") {

                    $paymentType = 'bank';

                    $bankId = str_replace('bank_', '', $item['payment_type']);
                }

                $expense->details()->create([
                    'item_name' => $item['item_name'],
                    'amount' => $item['amount'],
                    'note' => $item['note'] ?? null,
                    'payment_type' => $paymentType,
                    'bank_account_id' => $bankId
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

        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('pages.expenses.edit', compact(
            'expense',
            'bankAccounts'
        ));
    }

    private function handleJournalNew($expense)
    {
        $expenseAccount = $this->getBranchExpenseAccount(
            $expense->branch_id
        );

        $lines = [];

        $totalExpense = 0;

        foreach ($expense->details as $detail) {

            $amount = $detail->amount;

            $totalExpense += $amount;

            if ($detail->payment_type === 'cash') {

                $cashAccounts = [
                    1 => '101.00.01',   // Pasuruan\
                    2 => '101.00.011',  // Sandang Ayu
                ];

                $creditAccount = $cashAccounts[$expense->branch_id] ?? '101.00.00';
            } else {

                $bank = \App\Models\BankAccount::find(
                    $detail->bank_account_id
                );

                if (!$bank) {
                    throw new \Exception(
                        'Bank account tidak ditemukan'
                    );
                }

                $creditAccount = $bank->account_code;
            }

            $lines[] = [
                'account' => $creditAccount,
                'debit' => 0,
                'credit' => $amount,
            ];
        }

        $lines[] = [
            'account' => $expenseAccount,
            'debit' => $totalExpense,
            'credit' => 0,
        ];

        \App\Helpers\AccountingHelper::post([
            'date' => $expense->date,
            'reference' => $expense->code,
            'description' => 'Update Pengeluaran ' . $expense->code,
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'branch_id' => $expense->branch_id,
            'lines' => $lines
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:1',
            'items.*.payment_type' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {

            $expense = Expense::with('details')->findOrFail($id);

            // =========================
            // REVERSE JOURNAL LAMA
            // =========================

            $journal = Journal::with('items')
                ->where('source_type', 'expense')
                ->where('source_id', $expense->id)
                ->whereNull('reversal_of')
                ->first();

            if ($journal) {
                \App\Helpers\AccountingHelper::reverse(
                    $journal,
                    'Edit Expense'
                );
            }

            // =========================
            // HAPUS DETAIL LAMA
            // =========================

            $expense->details()->delete();

            // =========================
            // UPDATE HEADER
            // =========================

            $expense->update([
                'date' => $request->date,
                'total_amount' => 0
            ]);

            $total = 0;

            // =========================
            // INSERT DETAIL BARU
            // =========================

            foreach ($request->items as $item) {

                $paymentType = 'cash';
                $bankId = null;

                if ($item['payment_type'] != 'cash') {

                    $paymentType = 'bank';

                    $bankId = str_replace(
                        'bank_',
                        '',
                        $item['payment_type']
                    );
                }

                $expense->details()->create([
                    'item_name' => $item['item_name'],
                    'amount' => $item['amount'],
                    'note' => $item['note'] ?? null,
                    'payment_type' => $paymentType,
                    'bank_account_id' => $bankId,
                ]);

                $total += $item['amount'];
            }

            // =========================
            // UPDATE TOTAL
            // =========================

            $expense->update([
                'total_amount' => $total
            ]);

            // reload detail terbaru
            $expense->load('details');

            // =========================
            // POST JOURNAL BARU
            // =========================

            $this->handleJournalNew($expense);
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
