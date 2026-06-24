<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Helpers\AccountingHelper;
use Illuminate\Support\Facades\DB;

class CashMutationController extends BaseController
{
    public function index()
    {
        $mutations = CashMutation::with(['fromBank', 'toBank'])
            ->latest()
            ->paginate(20);

        return view('pages.mutasi-kas.index', compact('mutations'));
    }

    public function create()
    {
        $banks = BankAccount::where('is_active', true)->get();

        return view('pages.mutasi-kas.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'from_bank_account_id' => 'nullable|exists:bank_accounts,id',
            'to_bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            $reference = 'MUT-' . now()->format('YmdHis');

            $mutation = CashMutation::create([
                'date' => $request->date,
                'reference' => $reference,
                'from_bank_account_id' => $request->from_bank_account_id,
                'to_bank_account_id' => $request->to_bank_account_id,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            $user = auth()->user();
            $branchId = $user->profile->branch_id;

            $cashAccounts = [
                1 => '101.00.01',   // Pasuruan
                3 => '101.00.08',  // Sandang Ayu
            ];

            $cashAccount = $cashAccounts[$branchId] ?? '101.00.00';

            $fromCode = $request->from_bank_account_id
                ? BankAccount::find($request->from_bank_account_id)->account_code
                : $cashAccount;

            $toCode = $request->to_bank_account_id
                ? BankAccount::find($request->to_bank_account_id)->account_code
                : $cashAccount;

            AccountingHelper::post([
                'date' => $request->date,
                'reference' => $reference,
                'description' => 'Mutasi Kas',
                'source_type' => CashMutation::class,
                'source_id' => $mutation->id,
                'lines' => [
                    [
                        'account' => $toCode,
                        'debit' => $request->amount,
                        'credit' => 0,
                    ],
                    [
                        'account' => $fromCode,
                        'debit' => 0,
                        'credit' => $request->amount,
                    ]
                ]
            ]);
        });

        return redirect()
            ->route('mutasi-kas.index')
            ->with('success', 'Mutasi kas berhasil disimpan');
    }

    public function edit(CashMutation $cashMutation)
    {
        $banks = BankAccount::where('is_active', true)->get();

        return view('pages.mutasi-kas.edit', compact('cashMutation', 'banks'));
    }

    public function update(Request $request, CashMutation $cashMutation)
    {
        $request->validate([
            'date' => 'required|date',
            'from_bank_account_id' => 'nullable|exists:bank_accounts,id',
            'to_bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        if ($request->from_bank_account_id == $request->to_bank_account_id) {
            return back()
                ->withErrors([
                    'from_bank_account_id' => 'Rekening asal dan tujuan tidak boleh sama'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $cashMutation) {

            // reverse jurnal lama
            $oldJournal = \App\Models\Journal::where([
                'source_type' => CashMutation::class,
                'source_id' => $cashMutation->id,
            ])->first();

            if ($oldJournal) {
                \App\Helpers\AccountingHelper::reverse(
                    $oldJournal,
                    'Reversal Edit Mutasi Kas'
                );
            }

            // update mutasi
            $cashMutation->update([
                'date' => $request->date,
                'from_bank_account_id' => $request->from_bank_account_id,
                'to_bank_account_id' => $request->to_bank_account_id,
                'amount' => $request->amount,
                'note' => $request->note,
            ]);

            // coa asal
            $user = auth()->user();
            $branchId = $user->profile->branch_id;

            $cashAccounts = [
                1 => '101.00.01',   // Pasuruan
                3 => '101.00.08',  // Sandang Ayu
            ];

            $cashAccount = $cashAccounts[$branchId] ?? '101.00.00';

            $fromCode = $request->from_bank_account_id
                ? BankAccount::find($request->from_bank_account_id)->account_code
                : $cashAccount;

            $toCode = $request->to_bank_account_id
                ? BankAccount::find($request->to_bank_account_id)->account_code
                : $cashAccount;

            // jurnal baru
            AccountingHelper::post([
                'date' => $request->date,
                'reference' => $cashMutation->reference,
                'description' => 'Edit Mutasi Kas',
                'source_type' => CashMutation::class,
                'source_id' => $cashMutation->id,
                'lines' => [
                    [
                        'account' => $toCode,
                        'debit' => $request->amount,
                        'credit' => 0,
                    ],
                    [
                        'account' => $fromCode,
                        'debit' => 0,
                        'credit' => $request->amount,
                    ]
                ]
            ]);
        });

        return redirect()
            ->route('mutasi-kas.index')
            ->with('success', 'Mutasi kas berhasil diupdate');
    }

    public function destroy(CashMutation $cashMutation)
    {
        DB::transaction(function () use ($cashMutation) {

            // ambil jurnal
            $journal = \App\Models\Journal::where([
                'source_type' => CashMutation::class,
                'source_id' => $cashMutation->id,
            ])->orderBy('id', "desc")->first();

            // reverse jurnal
            if ($journal) {
                \App\Helpers\AccountingHelper::reverse(
                    $journal,
                    'Reversal Hapus Mutasi Kas'
                );
            }

            // hapus mutasi
            $cashMutation->delete();
        });

        return redirect()
            ->route('mutasi-kas.index')
            ->with('success', 'Mutasi kas berhasil dihapus');
    }
}
