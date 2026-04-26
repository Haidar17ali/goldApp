@extends('adminlte::page')

@section('title', 'Tambah Harga Emas')

@section('content_header')
    <h1>Tambah Harga Emas</h1>
@stop

@section('content')

    <form action="{{ route('set-harga.simpan') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">

                {{-- Tanggal --}}
                <div class="row">
                    <div class="col-md-6">
                        <label>Tanggal Aktif</label>
                        <input type="date" name="active_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label>Harga Emas 24K (per gram)</label>
                        <input type="number" name="price_24k" class="form-control" placeholder="Contoh: 1000000" required>
                    </div>
                </div>

            </div>

            <div class="text-right card-footer">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </div>

    </form>

@stop
