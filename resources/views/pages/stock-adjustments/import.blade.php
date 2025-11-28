@extends('adminlte::page')

@section('title', 'Import Stock Opname')

@section('content_header')
    <h1 class="fw-bold">Import Stock Opname</h1>
@stop

@section('content')

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong>
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- UPLOAD FORM --}}
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <strong>Upload File Excel Stock Opname</strong>
        </div>
        <div class="card-body">

            <form action="{{ route('opname.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Pilih File Excel</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" name="file"
                        accept=".xlsx,.xls" required>

                    @error('file')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-upload"></i> Import Data
                </button>
            </form>

        </div>
    </div>

    <hr>

    {{-- FORMAT INFORMASI --}}
    <h5 class="mt-4"><strong>📄 Format Excel (Format A)</strong></h5>

    <div class="alert alert-info w-75">
        <i class="fas fa-info-circle"></i>
        <strong>Catatan:</strong> <br>
        - <b>system_qty tidak perlu diinput</b>, sistem akan menghitung otomatis. <br>
        - Kolom <b>weight</b> tidak boleh dikosongkan. <br>
        - Kolom <b>type</b> harus diisi: <code>new</code> atau <code>second</code>.
    </div>

    <table class="table table-bordered w-75">
        <thead class="table-light">
            <tr>
                <th>product</th>
                <th>karat</th>
                <th>type</th>
                <th>actual_qty</th>
                <th>weight (optional)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>cincin</td>
                <td>8K</td>
                <td>new</td>
                <td>12</td>
                <td>2.40</td>
            </tr>
            <tr>
                <td>gelang</td>
                <td>24K</td>
                <td>second</td>
                <td>5</td>
                <td>1.2</td>
            </tr>
        </tbody>
    </table>

@stop
