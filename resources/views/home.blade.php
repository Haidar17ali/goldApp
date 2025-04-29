@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
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
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Total Karyawan" text="{{ count($employees) }}" icon="fas fa-user-check"
                theme="success" />
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Karyawan Laki-laki" text="{{ count($employees->where('gender', 1)) }}"
                icon="fas fa-male" theme="primary" />
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Karyawan Perempuan" text="{{ count($employees->where('gender', 0)) }}"
                icon="fas fa-female" theme="pink" />
        </div>
    </div>

    <div class="row">
        <!-- Surat Jalan Section -->
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Surat Jalan Masuk" text="{{ count($roadPermits->where('date', now())) }}"
                icon="fas fa-truck-loading" theme="warning" />
        </div>
        {{-- <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Belum Grading" text="#" icon="fas fa-hourglass-half" theme="danger" />
        </div> --}}
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Sudah Grading" text="{{ count($lpbs) }}" icon="fas fa-check-circle"
                theme="success" />
            {{-- <div class="progress progress-xs">
                <div class="progress-bar bg-success" style="width: 15%"></div>
            </div> --}}
            {{-- <small>15% Grading</small> --}}
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-small-box title="Truk Dibongkar"
                text="{{ count($roadPermits->where('status', 'Sudah Dibongkar')) }}/{{ count($roadPermits->where('status', 'Proses Bongkar')) }}"
                icon="fas fa-truck" theme="#" />
        </div>
    </div>

    <div class="row">
        <!-- Top NPWP Chart -->
        <!-- Top NPWP Chart + Table -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 7 NPWP Pembayaran</h3>
                </div>
                <div class="card-body">
                    <canvas id="topNpwpChart" height="150"></canvas>

                    <hr>

                    <h5 class="mt-4 mb-2">Detail Data</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama NPWP</th>
                                    <th>Total Uang Transfer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topNpwpData as $index => $npwp)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $npwp->nama_npwp }}</td>
                                        <td>Rp {{ number_format($npwp->total_uang, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Stok Sengon</h3>
                </div>
                <div class="card-body">
                    <canvas id="stokSengonChart"></canvas>
                    <hr>

                    <h5>Belum Terpakai:</h5>
                    <ul>
                        <li>Reject 130: {{ $stokBelumTerpakai['reject_130'] }} m³</li>
                        <li>Super 130: {{ $stokBelumTerpakai['super_130'] }} m³</li>
                        <li>Super 260: {{ $stokBelumTerpakai['super_260'] }} m³</li>
                    </ul>

                    <h5 class="mt-3">Terpakai Hari Ini:</h5>
                    <ul>
                        <li>Reject 130: {{ $stokTerpakaiHariIni['reject_130'] }} m³</li>
                        <li>Super 130: {{ $stokTerpakaiHariIni['super_130'] }} m³</li>
                        <li>Super 260: {{ $stokTerpakaiHariIni['super_260'] }} m³</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Top NPWP
        const topNpwpChart = document.getElementById('topNpwpChart').getContext('2d');
        const chart = new Chart(topNpwpChart, {
            type: 'bar',
            data: {
                labels: @json($topNpwpData->pluck('nama_npwp')),
                datasets: [{
                    label: 'Total Uang Transfer',
                    data: @json($topNpwpData->pluck('total_uang')),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
    <!-- Chart JS -->
    <script>
        const ctx = document.getElementById('stokSengonChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Reject 130', 'Super 130', 'Super 260'],
                datasets: [{
                    data: [
                        {{ $stokBelumTerpakai['reject_130'] }},
                        {{ $stokBelumTerpakai['super_130'] }},
                        {{ $stokBelumTerpakai['super_260'] }},
                    ],
                    backgroundColor: [
                        '#ff6384', '#4bc0c0', '#ffcd56'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Komposisi Stok Sengon Belum Terpakai'
                    }
                }
            }
        });
    </script>
@stop
