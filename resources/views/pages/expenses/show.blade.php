@extends('adminlte::page')

@section('title', 'Detail Pengeluaran')

@section('content_header')
    <h1>Detail Pengeluaran</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Kode:</strong><br>
                {{ $expense->code }}
            </div>

            <div class="col-md-4">
                <strong>Tanggal:</strong><br>
                {{ $expense->date }}
            </div>

            <div class="col-md-4">
                <strong>Cabang:</strong><br>
                {{ $expense->branch->name ?? '-' }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Total:</strong><br>
                <h4 class="text-success">
                    Rp {{ number_format($expense->total_amount, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <hr>

        {{-- DETAIL ITEMS --}}
        <h5>Rincian Pengeluaran</h5>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Keterangan</th>
                        <th width="200">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expense->details as $detail)
                        <tr>
                            <td>{{ $detail->item_name }}</td>
                            <td>{{ $detail->note }}</td>
                            <td>
                                Rp {{ number_format($detail->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- JOURNAL --}}
{{-- @if($journal)
<div class="card">
    <div class="card-header">
        <h5>Jurnal Akuntansi</h5>
    </div>
    <div class="card-body">

        <div class="mb-2">
            <strong>Referensi:</strong> {{ $journal->reference }} <br>
            <strong>Tanggal:</strong> {{ $journal->date }}
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th width="200">Debit</th>
                        <th width="200">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journal->items as $item)
                        <tr>
                            <td>
                                {{ $item->account->code }} - {{ $item->account->name }}
                            </td>
                            <td>
                                Rp {{ number_format($item->debit, 0, ',', '.') }}
                            </td>
                            <td>
                                Rp {{ number_format($item->credit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endif --}}

@stop