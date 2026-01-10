@extends('adminlte::page')

@section('title', 'Detail Stok')

@section('content_header')
<h1>Detail Stok</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <strong>
            {{ strtoupper($stocks->first()->product_name ?? '-') }}
            | {{ $stocks->first()->karat_name ?? '-' }}
            | {{ strtoupper(request('type')) }}
        </strong>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>SKU</th>
                    <th>Gram</th>
                    <th>Lokasi</th>
                    <th>Tipe Stok</th>
                    <th>Qty</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $row)
                <tr class="text-center">
                    <td>{{ $row->sku }}</td>
                    <td>{{ number_format($row->gram, 3) }}</td>
                    <td>{{ $row->location_name }}</td>
                    <td>
                        <span class="badge badge-info">
                            {{ strtoupper($row->stock_type) }}
                        </span>
                    </td>
                    <td>{{ number_format($row->quantity, 3) }}</td>
                    <td>
                        <a href="{{ route('varian-produk.barcode-form', $row->variant_id) }}"
                           target="_blank"
                           class="btn btn-sm btn-success">
                            <i class="fas fa-barcode"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $stocks->links() }}
    </div>
</div>
@endsection
