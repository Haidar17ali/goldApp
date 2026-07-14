@extends('adminlte::page')

@section('title', 'Penjualan Online - ' . $marketplace->name)

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>

            <h1 class="mb-0">
                <i class="mr-2 fas fa-store text-primary"></i>
                Penjualan {{ $marketplace->name }}
            </h1>

            <small class="text-muted">
                Daftar transaksi penjualan dari marketplace {{ $marketplace->name }}
            </small>

        </div>

        <div>

            <a href="{{ route('penjualan.online.buat', $marketplace->id) }}" class="btn btn-primary">

                <i class="mr-1 fas fa-plus"></i>

                Transaksi

            </a>
            <a href="#" class="btn btn-success">

                <i class="mr-1 fas fa-sync-alt"></i>

                Sinkronisasi

            </a>

        </div>

    </div>

@stop


@section('content')

    @include('pages.online-sales._cards')

    @include('pages.online-sales._filter')

    <div class="card card-outline card-primary">

        <div class="card-header">

            <h3 class="card-title">

                <i class="mr-1 fas fa-list"></i>

                Daftar Transaksi

            </h3>

        </div>

        <div class="p-0 card-body">

            @include('pages.online-sales._table')

        </div>

        @if ($transactions->hasPages())
            <div class="card-footer">

                {{ $transactions->links() }}

            </div>
        @endif

    </div>

@stop


@section('css')

    <style>
        .small-box {

            border-radius: 12px;

        }

        .table td {

            vertical-align: middle;

        }

        .badge {

            font-size: 12px;

        }

        .avatar-customer {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background: #0d6efd;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;

        }
    </style>

@stop

@section('js')

    @include('pages.online-sales._scripts')

@stop
