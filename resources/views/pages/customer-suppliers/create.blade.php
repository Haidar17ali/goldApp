@extends('adminlte::page')

@section('title', 'Tambah Customer / Supplier')

@section('content_header')
    <h1 class="fw-bold">Tambah {{ $type }}</h1>
@stop

@section('content')
    <div class="card shadow-lg">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

            <form action="{{ route('customer-supplier.simpan', $type) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                        placeholder="Masukkan nama" required>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}"
                        placeholder="Masukkan nomor telepon">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">Alamat</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Masukkan alamat">{{ old('address') }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary me-2 mr-1">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop
