@extends('adminlte::page')

@section('title', 'Detail Konversi Gabung Emas')

@section('content_header')
    <h1 class="fw-bold">Detail Konversi Gabung Emas</h1>
@stop

@section('content')

    {{-- HEADER INFO --}}
    <div class="shadow card">
        <div class="card-header">
            <h4 class="mb-0">Informasi Header</h4>
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th width="250">Catatan</th>
                    <td>{{ $conversion->note ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $conversion->user->username ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ $conversion->created_at->format('d M Y H:i') }}</td>
                </tr>
            </table>

        </div>
    </div>

    {{-- INPUT PER KARAT --}}
    <div class="mt-4 shadow card">
        <div class="card-header">
            <h4 class="mb-0">Detail Input (Per Karat)</h4>
        </div>

        <div class="card-body">
            @php
                $groupedInputs = $conversion->inputs->groupBy(fn($item) => $item->productVariant->karat->name);
            @endphp

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Karat</th>
                        <th>Total Berat (Gram)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedInputs as $karatName => $items)
                        @php
                            $totalWeight = $items->sum(function ($item) {
                                return $item->productVariant->gram * $item->qty;
                            });
                        @endphp
                        <tr>
                            <td>{{ $karatName }}</td>
                            <td>{{ number_format($totalWeight, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3 fw-bold">
                Total Input:
                {{ number_format($conversion->inputs->sum(fn($i) => $i->productVariant->gram * $i->qty), 3) }}
                Gram
            </div>
        </div>
    </div>

    {{-- DETAIL INPUT (OPSIONAL – PER ITEM) --}}
    <div class="mt-4 shadow card">
        <div class="card-header">
            <h4 class="mb-0">Detail Item Input</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Karat</th>
                        <th>Gram</th>
                        <th>Qty</th>
                        <th>Total Gram</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conversion->inputs as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->productVariant->product->name }}</td>
                            <td>{{ $detail->productVariant->karat->name }}</td>
                            <td>{{ number_format($detail->productVariant->gram, 3) }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>
                                {{ number_format($detail->productVariant->gram * $detail->qty, 3) }}
                            </td>
                            <td>{{ $detail->note ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- BUTTON BACK --}}
    <div class="mt-3">
        <a href="{{ route('keluar-etalase.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

@stop
