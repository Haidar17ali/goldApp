@extends('adminlte::page')

@section('title', 'Export Database')

@section('content_header')
    <h1>Export Database Manual</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if (session('success'))
                <div class="alert alert-success mb-2">
                    {{ session('success') }}
                </div>
            @endif


        </div>

        <div class="card-body">
            <h5>Daftar File Backup:</h5>

            @if (count($files))
                <ul class="list-group">
                    @foreach ($files as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ basename($file) }}
                            <a href="{{ route('backup.download', basename($file)) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Belum ada file backup tersedia.</p>
            @endif
        </div>
    </div>
@stop
