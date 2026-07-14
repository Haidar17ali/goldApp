@extends('adminlte::page')

@section('title', 'Tambah Marketplace')

@section('content_header')

    <div class="d-flex justify-content-between">

        <h1>

            <i class="mr-2 fas fa-plus-circle"></i>

            Tambah Marketplace

        </h1>

        <a href="{{ route('marketplace.index') }}" class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Kembali

        </a>

    </div>

@stop

@section('content')

    <form action="{{ route('marketplace.simpan') }}" method="POST" enctype="multipart/form-data">

        @csrf

        @include('pages.marketplaces._form')

    </form>

@stop
