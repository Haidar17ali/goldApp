@extends('adminlte::page')

@section('title', 'Stok Emas')

@section('content_header')
    <h1>Stok Emas</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        {{-- SEARCH --}}
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari produk..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <select name="type" class="form-control">
                    <option value="">-- Semua Tipe --</option>
                    <option value="new" {{ request('type') == 'new' ? 'selected' : '' }}>NEW</option>
                    <option value="sepuh" {{ request('type') == 'sepuh' ? 'selected' : '' }}>SEPUH</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary">Search</button>
            </div>
        </form>

        {{-- TABLE --}}
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Kadar</th>
                    <th>Tipe</th>
                    <th>Total Gram</th>
                    <th>Total Qty</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ strtoupper($row->product_name) }}</td>
                    <td>{{ $row->karat_name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $row->variant_type == 'new' ? 'success' : 'warning' }}">
                            {{ strtoupper($row->variant_type) }}
                        </span>
                    </td>
                    <td>{{ number_format($row->total_weight, 3) }} gr</td>
                    <td>{{ number_format($row->total_qty, 0) }} pcs</td>
                    <td>
                        <a href="{{ route('stocks.detail', [
                            'product' => $row->product_id,
                            'karat' => $row->karat_name,
                            'type' => $row->variant_type
                        ]) }}" class="btn btn-sm btn-primary">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>

        {{ $stocks->links() }}
    </div>
</div>

@stop
