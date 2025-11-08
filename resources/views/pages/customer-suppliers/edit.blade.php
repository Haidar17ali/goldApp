@extends('adminlte::page')

@section('title', 'Edit ' . ucfirst($type))

@section('content_header')
    <h1 class="fw-bold">Edit {{ ucfirst($type) }}</h1>
@stop

@section('content')
    <div class="card shadow-lg">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer-supplier.update', ['type' => $type, 'id' => $item->id]) }}" method="POST">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control"
                        value="{{ old('phone_number', $item->phone_number) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $item->address) }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('customer-supplier.index', $type) }}" class="btn btn-secondary mr-1 me-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
