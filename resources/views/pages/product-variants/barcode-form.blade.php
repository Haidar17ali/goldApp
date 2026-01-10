@extends('adminlte::page')

@section('title', 'Cetak Barcode')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Cetak Barcode</h5>
        </div>
        <form action="{{ route('varian-produk.barcode-print', $item->id) }}" method="POST" target="_blank">
            @csrf
            <div class="card-body">
                <p><b>Produk:</b> {{ $item->product->name }}</p>
                <p><b>Kadar:</b> {{ $item->karat?->name ?? '-' }}</p>
                <p><b>Berat:</b> {{ $item->gram }} gr</p>
                <p><b>SKU:</b> {{ $item->sku }}</p>
                <p><b>Barcode:</b> {{ $item->barcode }}</p>

                <div class="form-group">
                    <label>Jumlah Barcode</label>
                    <input type="number" name="qty" class="form-control" value="1" min="1" max="100"
                        required>
                </div>
            </div>

            <div class="text-right card-footer">
                <button class="btn btn-success">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </form>
    </div>
@endsection
