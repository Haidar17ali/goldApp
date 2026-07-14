@extends('adminlte::page')

@section('title', 'Edit Marketplace')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <h1>
            <i class="mr-2 fas fa-edit"></i>
            Edit Marketplace
        </h1>

        <a href="{{ route('marketplace.index') }}" class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Kembali

        </a>

    </div>
@stop

@section('content')

    <form action="{{ route('marketplace.update', $marketplace) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('patch')

        @include('pages.marketplaces._form')

    </form>

@stop
