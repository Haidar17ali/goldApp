@extends('adminlte::page')

@section('title', 'Trial Balance')

@section('content_header')
    <h1>Neraca Saldo (Trial Balance)</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            {{-- 🔍 FILTER --}}
            <form method="GET" action="{{ route('accounting.trial-balance') }}">
                <div class="row">

                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary">Filter</button>
                    </div>

                </div>
            </form>

            <hr>

            {{-- 📊 TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="120">Kode</th>
                            <th>Nama Akun</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp

                        @forelse($accounts as $acc)
                            @php
                                $totalDebit += $acc->total_debit;
                                $totalCredit += $acc->total_credit;
                            @endphp

                            <tr>
                                <td>{{ strtoupper($acc->code) }}</td>
                                <td>{{ ucfirst($acc->name) }}</td>

                                <td class="text-right">
                                    {{ number_format($acc->total_debit, 0, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($acc->total_credit, 0, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($acc->balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- 🔥 TOTAL --}}
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-center">TOTAL</th>

                            <th class="text-right">
                                {{ number_format($totalDebit, 0, ',', '.') }}
                            </th>

                            <th class="text-right">
                                {{ number_format($totalCredit, 0, ',', '.') }}
                            </th>

                            <th class="text-right">
                                {{ number_format($totalDebit - $totalCredit, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

@stop
