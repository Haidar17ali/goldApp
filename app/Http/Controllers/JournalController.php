<?php

namespace App\Http\Controllers;

use App\Helpers\AccountingHelper;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends BaseController
{
    public function index(Request $request)
    {
        $query = Journal::with(['items.account', 'reversedBy', 'original'])
            ->orderBy('id', 'desc')
            ->orderBy('date', 'desc');

        // 🔎 Filter tanggal
        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // 🔎 Filter pencarian jurnal
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $journals = $query->paginate(15)->withQueryString();

        return view('pages.journals.index', compact('journals'));
    }

    public function create()
    {
        // ✅ Hanya akun leaf (tidak punya child)
        $accounts = ChartOfAccount::doesntHave('children')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('pages.journals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        // ✅ Validasi dasar
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        $lines = [];

        foreach ($request->items as $item) {

            $account = ChartOfAccount::findOrFail($item['account_id']);

            $debit = (float) ($item['debit'] ?? 0);
            $credit = (float) ($item['credit'] ?? 0);

            // 🔹 Skip baris kosong
            if ($debit == 0 && $credit == 0) {
                continue;
            }

            $lines[] = [
                'account' => $account->code,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        // ❌ Minimal 2 baris
        if (count($lines) < 2) {
            return back()->withErrors('Minimal harus ada 2 baris jurnal.')
                ->withInput();
        }

        try {

            AccountingHelper::post([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'source_type' => 'manual',
                'lines' => $lines
            ]);

            return redirect()
                ->route('jurnal.index')
                ->with('status', 'saved');
        } catch (\Exception $e) {

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function show(Journal $journal)
    {
        $journal->load('items.account');

        return view('pages.journals.detail', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        // ambil akun leaf
        $accounts = ChartOfAccount::doesntHave('children')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // ambil detail jurnal
        $journal->load('items');

        return view('pages.journals.edit', compact('journal', 'accounts'));
    }

    public function update(Request $request, Journal $journal)
    {
        // ================= VALIDASI =================
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
        ]);

        // ================= PROTEKSI =================

        // Tidak boleh edit jurnal reversal
        if ($journal->is_reversal) {
            return back()
                ->withErrors('Jurnal reversal tidak boleh diedit')
                ->withInput();
        }

        // Tidak boleh edit jika sudah pernah direversal
        if ($journal->reversedBy) {
            return back()
                ->withErrors('Jurnal sudah pernah direversal')
                ->withInput();
        }

        // ================= PREPARE DATA =================
        $lines = [];

        foreach ($request->items as $item) {

            $account = ChartOfAccount::findOrFail($item['account_id']);

            $debit = (float) ($item['debit'] ?? 0);
            $credit = (float) ($item['credit'] ?? 0);

            // Skip baris kosong
            if ($debit == 0 && $credit == 0) {
                continue;
            }

            // Tidak boleh debit & kredit sekaligus
            if ($debit > 0 && $credit > 0) {
                return back()
                    ->withErrors('Debit dan kredit tidak boleh diisi bersamaan dalam satu baris.')
                    ->withInput();
            }

            $lines[] = [
                'account' => $account->code,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        // Minimal 2 baris
        if (count($lines) < 2) {
            return back()
                ->withErrors('Minimal harus ada 2 baris jurnal.')
                ->withInput();
        }

        // Validasi balance
        $totalDebit = collect($lines)->sum('debit');
        $totalCredit = collect($lines)->sum('credit');

        if ($totalDebit != $totalCredit) {
            return back()
                ->withErrors('Jurnal tidak balance (debit harus sama dengan kredit).')
                ->withInput();
        }

        // ================= PROCESS =================
        try {

            DB::transaction(function () use ($request, $journal, $lines) {

                // STEP 1: Reverse jurnal lama
                AccountingHelper::reverse($journal);

                // STEP 2: Buat jurnal baru hasil edit
                AccountingHelper::post([
                    'date' => $request->date,
                    'reference' => $request->reference,
                    'description' => '[EDIT] ' . $request->description,
                    'source_type' => 'manual_edit',
                    'source_id' => $journal->id,
                    'lines' => $lines
                ]);
            });

            return redirect()
                ->route('jurnal.index')
                ->with('success', 'Jurnal berhasil diupdate (reversal + jurnal baru)');
        } catch (\Exception $e) {

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {

            $journal = Journal::with('items')->findOrFail($id);

            DB::transaction(function () use ($journal) {

                // ❌ Tidak boleh hapus jurnal reversal
                if ($journal->is_reversal) {
                    throw new \Exception('Jurnal reversal tidak boleh dihapus');
                }

                // ❌ Tidak boleh jika sudah pernah direversal
                if ($journal->reversedBy) {
                    throw new \Exception('Jurnal sudah pernah direversal');
                }

                // 🔥 REVERSE + LABEL DELETE
                AccountingHelper::reverse($journal, '[HAPUS]');
            });

            return redirect()
                ->route('jurnal.index')
                ->with('success', 'Jurnal berhasil dihapus (reversal)');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withErrors($e->getMessage());
        }
    }
}
