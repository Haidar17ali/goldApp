@extends('adminlte::page')

@section('title', 'Payroll')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Payroll Bulanan</h1>

        <a href="{{ route('payroll.generate') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Gaji Bulan Ini
        </a>
    </div>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rekap Payroll per Bulan</h3>
        </div>

        <div class="p-0 card-body table-responsive">

            <table class="table table-hover text-nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Karyawan</th>
                        <th>Total Gaji</th>
                        <th>Total Bonus</th>
                        <th>Total Potongan</th>
                        <th>Total Dibayarkan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($months as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->bulan_nama }}</strong>
                            </td>

                            <td>{{ $m->jumlah_karyawan }}</td>

                            <td>Rp {{ number_format($m->total_gaji, 0, ',', '.') }}</td>

                            <td>Rp {{ number_format($m->total_bonus, 0, ',', '.') }}</td>

                            <td>Rp {{ number_format($m->total_potongan, 0, ',', '.') }}</td>

                            <td>
                                <strong>
                                    Rp
                                    {{ number_format($m->total_gaji + $m->total_bonus - $m->total_potongan, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                <a href="{{ route('payroll.detail', $m->bulan) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data payroll
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

@stop
