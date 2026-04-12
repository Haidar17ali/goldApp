@extends('adminlte::page')

@section('title', 'Detail Stock Opname')

@section('content_header')
    <h1>Detail Stock Opname</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <a href="{{ route('opname.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>


            <form action="{{ route('barcode.cetak-form') }}" method="POST" target="_blank" class="float-right">
                @csrf

                @php $index = 0; @endphp

                @foreach ($adjustment->details as $d)
                    @if ($d->actual_qty > 0)
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $d->product_variant_id }}">
                        <input type="hidden" name="variants[{{ $index }}][qty]"
                            value="{{ (int) $d->actual_qty - $d->system_qty }}">
                        @php $index++; @endphp
                    @endif
                @endforeach

                <button type="submit" class="btn btn-primary btn-sm">
                    Print Barcode
                </button>
            </form>
        </div>

        <div class="card-body">

            {{-- HEADER --}}
            <table class="table mb-4 table-bordered table-sm">
                <tr>
                    <th width="200">Tanggal</th>
                    <td>{{ $adjustment->adjustment_date }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $adjustment->note }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $adjustment->created_by }}</td>
                </tr>
            </table>

            {{-- DETAIL --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="text-white bg-dark">
                        <tr>
                            <th>Produk</th>
                            <th>Karat</th>
                            <th>Berat</th>
                            <th>Type</th>
                            <th>System Qty</th>
                            <th>Actual Qty</th>
                            <th>Selisih</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPlus = 0;
                            $totalMinus = 0;
                        @endphp

                        @foreach ($adjustment->details as $d)
                            @php
                                $harga =
                                    \App\Helpers\GoldHelper::getHargaByKarat($d->productVariant->karat_id ?? null) ?? 0;
                                $weight = $d->productVariant->gram ?? 0;
                                $nilai = $d->difference * $weight * $harga;

                                if ($nilai > 0) {
                                    $totalPlus += $nilai;
                                }
                                if ($nilai < 0) {
                                    $totalMinus += abs($nilai);
                                }
                            @endphp

                            <tr>
                                <td>{{ $d->productVariant->product->name ?? '-' }}</td>
                                <td>{{ $d->productVariant->karat->name ?? '-' }}</td>
                                <td>{{ $d->productVariant->gram ?? 0 }} gr</td>
                                <td>{{ strtoupper($d->type) }}</td>
                                <td>{{ $d->system_qty }}</td>
                                <td>{{ $d->actual_qty }}</td>
                                <td>
                                    <span class="badge {{ $d->difference >= 0 ? 'badge-success' : 'badge-danger' }}">
                                        {{ $d->difference }}
                                    </span>
                                </td>
                                <td>{{ number_format($nilai, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-right">Total Selisih (+)</th>
                            <th>{{ number_format($totalPlus, 0) }}</th>
                        </tr>
                        <tr>
                            <th colspan="7" class="text-right">Total Selisih (-)</th>
                            <th>{{ number_format($totalMinus, 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

@stop
