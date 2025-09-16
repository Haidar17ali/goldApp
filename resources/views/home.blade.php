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
