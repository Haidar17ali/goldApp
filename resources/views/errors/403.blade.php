@extends('adminlte::page')

@section('title', '403 Dilarang')

@section('content_header')
    <h1>403 - Akses Ditolak</h1>
@stop

@section('content')
    <div class="text-center mt-5">
        {{-- SVG Ilustrasi: Akses Ditolak --}}
        <svg width="300" height="300" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="256" cy="256" r="256" fill="#f8d7da" />
            <path d="M331 181L181 331M181 181L331 331" stroke="#721c24" stroke-width="30" stroke-linecap="round" />
            <circle cx="256" cy="256" r="170" stroke="#721c24" stroke-width="30" fill="none" />
        </svg>

        <h2 class="mt-4 text-danger">Akses Ditolak</h2>
        <p class="text-muted">Sepertinya kamu tidak memiliki izin untuk mengakses halaman ini.</p>

        <div class="mt-4">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-home"></i> Ke Dashboard
            </a>
        </div>
    </div>
@stop
