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

    @can(['lpb.index'])
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
                        <form id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label for="start_date" class="form-label">Dari:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="last_date" class="form-label">Sampai:</label>
                                    <input type="date" class="form-control" id="last_date" name="last_date">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="chart-container">
                            <canvas id="stokSengonChart" width="10" height="10"></canvas>
                        </div>
                        <hr>

                        <div id="result-stok">
                            <h3>Stok Terpakai</h3>
                            <div id="stok-terpakai"></div>

                            <h3>Stok Belum Terpakai</h3>
                            <div id="stok-belum-terpakai"></div>
                        </div>

                        {{-- <h5>Belum Terpakai:</h5>
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
                        </ul> --}}
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">Ringkasan LPB Belum Terpakai</h3>
                </div>
                <div class="card-body p-0">
                    <div style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
                        <table class="table table-sm table-bordered table-striped m-0">
                            <thead style="position: sticky; top: 0; z-index: 1;" class="bg-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>No Kitir</th>
                                    <th>130 Afkir (m³)</th>
                                    <th>130 Super (m³)</th>
                                    <th>260 Super (m³)</th>
                                    <th>Total Kubikasi (m³)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAfkir130 = 0;
                                    $totalSuper130 = 0;
                                    $totalSuper260 = 0;
                                @endphp
                                @forelse ($lpbsBelumTerpakai as $index => $lpb)
                                    @php
                                        $totalAfkir130 += $lpb->stock_130_afkir;
                                        $totalSuper130 += $lpb->stock_130_super;
                                        $totalSuper260 += $lpb->stock_260_super;
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($lpb->date)->format('d-m-Y') }}</td>
                                        <td>{{ $lpb->no_kitir }}</td>
                                        <td>{{ number_format($lpb->stock_130_afkir, 4, ',', '.') }}</td>
                                        <td>{{ number_format($lpb->stock_130_super, 4, ',', '.') }}</td>
                                        <td>{{ number_format($lpb->stock_260_super, 4, ',', '.') }}</td>
                                        <td>{{ number_format($lpb->total_kubikasi, 4, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada LPB belum terpakai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="font-weight-bold text-center table-secondary">
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>{{ number_format($totalAfkir130, 4, ',', '.') }}</th>
                                    <th>{{ number_format($totalSuper130, 4, ',', '.') }}</th>
                                    <th>{{ number_format($totalSuper260, 4, ',', '.') }}</th>
                                    <th>{{ number_format($totalKubikasiLpbBelumTerpakai, 4, ',', '.') }} m³</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
                maintainAspectRatio: false, // biar ikut ukuran CSS
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
    <script>
        function getTodayDate() {
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function loadStockData(startDate, lastDate) {
            $.ajax({
                url: "{{ route('filter-stock') }}",
                data: {
                    start_date: startDate,
                    last_date: lastDate
                },
                success: function(data) {
                    let terpakai = data.stok_terpakai;
                    let belum = data.stok_belum_terpakai;
                    let totalTerpakai = terpakai.reject_130 + terpakai.super_130 + terpakai.super_260
                    let totalBelumTerpakai = belum.reject_130 + belum.super_130 + belum.super_260

                    $('#stok-terpakai').html(`
                    <p>Afkir 130: ${terpakai.reject_130.toFixed(4)} m³</p>
                    <p>Super 130: ${terpakai.super_130.toFixed(4)} m³</p>
                    <p>Super 260: ${terpakai.super_260.toFixed(4)} m³</p>
                    <p>Total: ${totalTerpakai.toFixed(4)} m³</p>
                `);

                    $('#stok-belum-terpakai').html(`
                    <p>Afkir 130: ${belum.reject_130.toFixed(4)} m³</p>
                    <p>Super 130: ${belum.super_130.toFixed(4)} m³</p>
                    <p>Super 260: ${belum.super_260.toFixed(4)} m³</p>
                    <p>Total: ${totalBelumTerpakai.toFixed(4)} m³</p>
                `);
                },
                error: function() {
                    $('#stok-terpakai').html('<p>Gagal memuat data</p>');
                    $('#stok-belum-terpakai').html('<p>Gagal memuat data</p>');
                }
            });
        }

        $(document).ready(function() {
            let today = getTodayDate();
            $('#start_date').val(today);
            $('#last_date').val(today);

            // Muat data stok otomatis saat halaman dibuka
            loadStockData(today, today);
        });

        $('#filter-form').on('submit', function(e) {
            e.preventDefault();

            let startDate = $('#start_date').val();
            let lastDate = $('#last_date').val();
            console.log(startDate);


            loadStockData(startDate, lastDate);
        });
    </script>
@stop
