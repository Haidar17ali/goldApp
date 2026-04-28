<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class FinancialStatementController extends Controller
{

    public function trialBalance($startDate = null, $endDate = null)
    {
        $accounts = $this->getTrialBalanceData($startDate, $endDate);

        return view('pages.Report.accounting.trial-balance', compact('accounts'));
    }

    private function getTrialBalanceData($startDate = null, $endDate = null)
    {
        $query = ChartOfAccount::withSum(['journalItems as total_debit' => function ($q) use ($startDate, $endDate) {
            $q->select(DB::raw('COALESCE(SUM(debit),0)'))
                ->when($startDate, fn($q) => $q->whereHas('journal', fn($j) => $j->whereDate('date', '>=', $startDate)))
                ->when($endDate, fn($q) => $q->whereHas('journal', fn($j) => $j->whereDate('date', '<=', $endDate)));
        }], 'debit')
            ->withSum(['journalItems as total_credit' => function ($q) use ($startDate, $endDate) {
                $q->select(DB::raw('COALESCE(SUM(credit),0)'))
                    ->when($startDate, fn($q) => $q->whereHas('journal', fn($j) => $j->whereDate('date', '>=', $startDate)))
                    ->when($endDate, fn($q) => $q->whereHas('journal', fn($j) => $j->whereDate('date', '<=', $endDate)));
            }], 'credit');

        $accounts = $query->get();

        $accounts->transform(function ($acc) {
            $debit = $acc->total_debit ?? 0;
            $credit = $acc->total_credit ?? 0;

            $acc->balance = $acc->normal_balance === 'debit'
                ? $debit - $credit
                : $credit - $debit;

            return $acc;
        });

        return $accounts;
    }
}
