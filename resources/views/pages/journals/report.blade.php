@extends('adminlte::page')

@section('title', 'Laporan Jurnal')

@section('content_header')
    <h1>Laporan Jurnal</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-body">

            <form method="GET" class="mb-3 row">

                <div class="col-md-3">
                    <label>Dari</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-3">
                    <label>Sampai</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary">
                        Filter
                    </button>
                    <a href="{{ route('jurnal.export', [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]) }}"
                        class="ml-2 btn btn-success"> <i class="fas fa-file-excel"></i>
                        Export Excel
                    </a>
                </div>

            </form>

            <div class="table-responsive">

                <table class="table table-bordered table-sm">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Referensi</th>
                            <th>Deskripsi</th>
                            <th>Sumber</th>
                            <th>Nama Akun</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Kredit</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp

                        @foreach ($journals as $journal)
                            @foreach ($journal->items as $item)
                                @php
                                    $totalDebit += $item->debit;
                                    $totalCredit += $item->credit;
                                @endphp

                                <tr>

                                    <td>
                                        {{ $journal->date }}
                                    </td>

                                    <td>
                                        {{ $journal->reference }}
                                    </td>

                                    <td>
                                        {{ $journal->description }}
                                    </td>

                                    <td>
                                        {{ strtoupper($journal->source_type) }}
                                    </td>

                                    <td>
                                        {{ $item->account->name ?? '-' }}
                                    </td>

                                    <td class="text-right">
                                        {{ number_format($item->debit, 0, ',', '.') }}
                                    </td>

                                    <td class="text-right">
                                        {{ number_format($item->credit, 0, ',', '.') }}
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">
                                TOTAL
                            </th>

                            <th class="text-right">
                                {{ number_format($totalDebit, 0, ',', '.') }}
                            </th>

                            <th class="text-right">
                                {{ number_format($totalCredit, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>

                </table>

            </div>

            <div class="mt-3">
                {{ $journals->withQueryString()->links() }}
            </div>

        </div>

    </div>

@stop
