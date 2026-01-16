@extends('adminlte::page')

@section('title', 'Detail Konversi Emas')

@section('content_header')
    <h1 class="fw-bold">Detail Konversi Emas</h1>
@stop

@section('content')

    <div class="shadow card">
        <div class="card-header">
            <h4 class="mb-0">Informasi Header</h4>
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th width="250">Stock Saat ini</th>
                    <td>
                        {{ $conversion->productVariant->product->name }} -
                        {{ $conversion->productVariant->karat->name }}
                        ({{ number_format($conversion->stock->weight, 3) }} Gram)
                    </td>
                </tr>
                <tr>
                    <th>Karat</th>
                    <td>{{ $conversion->productVariant->karat->name }}</td>
                </tr>
                <tr>
                    <th>Berat Input</th>
                    <td>{{ number_format($conversion->input_weight, 3) }} Gram</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $conversion->note ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $conversion->creator->username ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ $conversion->created_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- DETAIL OUTPUT --}}
    <div class="mt-4 shadow card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="mb-0">Detail Hasil Pecahan</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="10">#</th>
                        <th>Produk</th>
                        <th>Karat</th>
                        <th>Berat (Gram)</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conversion->outputs as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->productVariant->product->name }}</td>
                            <td>{{ $detail->productVariant->karat->name }}</td>
                            <td>{{ number_format($detail->weight, 3) }}</td>
                            <td>{{ $detail->note ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                <h5 class="fw-bold">
                    Total Output: {{ number_format($conversion->outputs->sum('weight'), 3) }} Gram
                </h5>
            </div>
        </div>
    </div>

    {{-- BUTTON BACK --}}
    <div class="mt-3">
        <a href="{{ route('konversi-emas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

@stop
