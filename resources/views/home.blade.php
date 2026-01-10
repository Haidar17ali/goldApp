@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
    @can('backup.export')
        <div class="row">
            <div class="col-md-12">
                <form method="POST" class="float-right" action="{{ route('backup.export') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-database"></i> Export Database Sekarang
                    </button>
                </form>
            </div>
        </div>
    @endcan

    <small class="text-muted">Last Updated: {{ now()->format('H:i') }}</small>

    <div class="float-right">
        <form method="GET" action="#">
            <select name="filter" class="form-control" onchange="this.form.submit()">
                <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="yesterday" {{ request('filter') == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                <option value="this_week" {{ request('filter') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
            </select>
        </form>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Users Section -->
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Total Users" text="{{ count($users) }}" icon="fas fa-users" theme="info" />
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-center">STOK EMAS DI BRANKAS</h3>
        </div>
        <div class="card-body">
            <div class="row">

                @forelse ($stockBrankas as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="small-box bg-warning h-100">
                            <div class="inner">
                                <h4 class="fw-bold mb-1">
                                    {{ number_format($item->total_gram, 2) }} gr
                                </h4>

                                <div class="text-sm">
                                    {{ $item->product_name ?? '-' }}
                                </div>

                                <div class="text-sm">
                                    Tipe {{ $item->type ?? '-' }}
                                </div>

                                <small class="text-muted">
                                    Karat {{ $item->karat_name ?? '-' }}
                                </small>
                            </div>

                            <div class="icon">
                                <i class="fas fa-ring"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12 text-center">
                            <h6>data emas dibrankas tidak ditemukan!</h6>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">STOK EMAS ETALASE</h3>
        </div>
        <div class="card-body">
            <div class="row">

                @forelse ($stocks as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="small-box bg-warning h-100">
                            <div class="inner">
                                <h4 class="fw-bold mb-1">
                                    {{ number_format($item->total_gram, 2) }} gr
                                </h4>

                                <div class="text-sm">
                                    {{ $item->product_name ?? '-' }}
                                </div>

                                <div class="text-sm">
                                    Tipe {{ $item->type ?? '-' }}
                                </div>

                                <small class="text-muted">
                                    Kadar {{ $item->karat_name ?? '-' }}
                                </small>
                            </div>

                            <div class="icon">
                                <i class="fas fa-ring"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12 text-center">
                            <h6>data emas tidak ditemukan!</h6>
                    </div>
                @endforelse

            </div>
        </div>
    </div>


@stop
<style>
    .chart-container {
        width: 400px;
        height: 300px;
        margin: 0 auto;
        /* Center horizontally */
        position: relative;
        /* Required by Chart.js for proper rendering */
    }

    #stokSengonChart {
        width: 100% !important;
        height: 100% !important;
    }
</style>

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop
