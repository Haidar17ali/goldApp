@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="mb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Dashboard</h1>
            <small class="text-muted">
                Last Updated: {{ now()->format('H:i') }}
            </small>
        </div>

        @can('backup.export')
            <form method="POST" action="{{ route('backup.export') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-database"></i> Backup Database
                </button>
            </form>
        @endcan
    </div>
@stop

@section('content')

    {{-- ===================================================== --}}
    {{-- INFO BOX --}}
    {{-- ===================================================== --}}
    <div class="mb-3 row">
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Total Users" text="{{ count($users) }}" icon="fas fa-users" theme="info" />
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- FILTER PERIODE --}}
    {{-- ===================================================== --}}
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">
                        <i class="fa fa-filter"></i> Terapkan Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- PENJUALAN --}}
    {{-- ===================================================== --}}
    <div class="mt-4 card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">Penjualan per Product & Karat</h3>
            <div class="card-tools">
                <span class="badge badge-info">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="p-0 card-body table-responsive">
            <table class="table mb-0 table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Product</th>
                        <th>Karat</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Total Gram</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salesByProduct as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->karat }}</td>
                            <td class="text-center fw-bold">{{ $row->qty }}</td>
                            <td class="text-right text-muted">
                                {{ number_format($row->total_gram, 2) }} gr
                            </td>
                            <td class="text-right fw-bold">
                                Rp {{ number_format($row->total_nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-right">Grand Total</td>
                        <td class="text-right">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- RINGKASAN PEMBAYARAN --}}
    {{-- ===================================================== --}}
    <h4 class="mt-4 mb-3">💰 Ringkasan Pembayaran</h4>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon">
                    <i class="fas fa-wallet"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Cash</span>
                    <span class="info-box-number fs-5">
                        Rp {{ number_format($cashTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Transfer per Bank</h3>
                </div>
                <div class="p-0 card-body">
                    <table class="table mb-0 table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th class="text-right">Total Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transferByBank as $bank)
                                <tr>
                                    <td>{{ $bank->bank_name }}</td>
                                    <td class="text-right">
                                        Rp {{ number_format($bank->total_transfer, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        Tidak ada transfer
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- masuk etalase --}}
    {{-- ===================================================== --}}
    {{-- EMAS MASUK ETALASE --}}
    {{-- ===================================================== --}}
    <div class="mt-4 card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">Emas Masuk Etalase per Product & Karat</h3>
            <div class="card-tools">
                <span class="badge badge-success">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="p-0 card-body table-responsive">
            <table class="table mb-0 table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Product</th>
                        <th>Karat</th>
                        <th class="text-center">Qty Item</th>
                        <th class="text-right">Total Gram</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($emasMasukEtalase as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->karat }}</td>
                            <td class="text-center fw-bold">{{ $row->qty }}</td>
                            <td class="text-right fw-bold text-success">
                                {{ number_format($row->total_gram, 2) }} gr
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Tidak ada emas masuk etalase
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="3" class="text-right">Total Emas Masuk</td>
                        <td class="text-right text-success">
                            {{ number_format($totalEmasMasuk, 2) }} gr
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- keluar etalase --}}
    {{-- ===================================================== --}}
    {{-- EMAS KELUAR ETALASE --}}
    {{-- ===================================================== --}}
    <div class="mt-4 card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title">Emas Keluar Etalase per Product & Karat</h3>
            <div class="card-tools">
                <span class="badge badge-danger">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="p-0 card-body table-responsive">
            <table class="table mb-0 table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Product</th>
                        <th>Karat</th>
                        <th class="text-center">Qty Keluar</th>
                        <th class="text-right">Total Gram</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($emasKeluarEtalase as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->karat }}</td>
                            <td class="text-center fw-bold">{{ $row->qty }}</td>
                            <td class="text-right fw-bold text-danger">
                                {{ number_format($row->total_gram, 2) }} gr
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Tidak ada emas keluar etalase
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="3" class="text-right">Total Emas Keluar</td>
                        <td class="text-right text-danger">
                            {{ number_format($totalEmasKeluar, 2) }} gr
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    @unlessrole('super-admin')
        {{-- ===================================================== --}}
        {{-- JAM & HARI RAMAI --}}
        {{-- ===================================================== --}}
        <div class="mt-4 row">
            @if ($jamPalingRamai)
                <div class="col-md-6">
                    <div class="info-box bg-info">
                        <span class="info-box-icon">
                            <i class="far fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Jam Paling Ramai</span>
                            <span class="info-box-number">
                                {{ sprintf('%02d:00', $jamPalingRamai->jam) }}
                                ({{ $jamPalingRamai->total_transaksi }} transaksi)
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($hariPalingRamai)
                <div class="col-md-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Hari Paling Ramai</span>
                            <span class="info-box-number">
                                {{ $hariMap[$hariPalingRamai->hari] }}
                                ({{ $hariPalingRamai->total_transaksi }} transaksi)
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===================================================== --}}
        {{-- PRODUK PALING LAKU --}}
        {{-- ===================================================== --}}
        <div class="mt-4 card">
            <div class="text-white card-header bg-dark">
                <strong>Produk Paling Laku</strong>
            </div>

            <div class="p-0 card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="text-center thead-light">
                        <tr>
                            <th width="5%">Rank</th>
                            <th>Produk</th>
                            <th>Karat</th>
                            <th>Qty</th>
                            <th>Total Gram</th>
                            <th>Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestProducts as $index => $item)
                            <tr>
                                <td class="text-center">
                                    {{ $index < 3 ? ['🥇', '🥈', '🥉'][$index] : $index + 1 }}
                                </td>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td class="text-center">{{ $item->karat }}</td>
                                <td class="text-center">{{ number_format($item->total_qty) }}</td>
                                <td class="text-right">{{ number_format($item->total_gram, 2) }} g</td>
                                <td class="text-right text-success">
                                    Rp {{ number_format($item->total_nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================================================== --}}
        {{-- PERFORMA KARYAWAN --}}
        {{-- ===================================================== --}}
        <div class="mt-4 card">
            <div class="text-white card-header bg-dark">
                <strong>Performa Karyawan</strong>
            </div>

            <div class="p-0 card-body table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <thead class="text-center thead-light">
                        <tr>
                            <th width="5%">Rank</th>
                            <th>Nama</th>
                            <th>Transaksi</th>
                            <th>Qty</th>
                            <th>Total Gram</th>
                            <th>Total Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeePerformance as $index => $row)
                            <tr>
                                <td class="text-center">
                                    {{ $index < 3 ? ['🥇', '🥈', '🥉'][$index] : $index + 1 }}
                                </td>
                                <td><strong>{{ $row->employee_name }}</strong></td>
                                <td class="text-center">{{ $row->total_transactions }}</td>
                                <td class="text-center">{{ number_format($row->qty) }}</td>
                                <td class="text-right">{{ number_format($row->total_gram, 2) }} g</td>
                                <td class="text-right text-success">
                                    Rp {{ number_format($row->total_nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endunlessrole

    {{-- ===================================================== --}}
    {{-- STOK ETALASE --}}
    {{-- ===================================================== --}}
    <div class="mt-4 card card-outline card-warning">
        <div class="text-center card-header">
            <strong>STOK EMAS ETALASE</strong>
        </div>

        <div class="card-body">
            <div class="row">
                @forelse ($stocks as $item)
                    <div class="mb-3 col-lg-3 col-md-4 col-sm-6">
                        <div class="border small-box bg-light h-100">
                            <div class="inner">
                                <h4 class="fw-bold">
                                    {{ number_format($item->total_gram, 2) }} gr
                                </h4>
                                <div>{{ (int) $item->total_qty }}pcs</div>
                                <div>{{ $item->product_name }}</div>
                                <small class="text-muted">
                                    Kadar {{ $item->karat_name }}
                                </small>
                            </div>
                            <div class="icon text-warning">
                                <i class="fas fa-ring"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center col-12 text-muted">
                        Data stok tidak ditemukan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop
