@extends('adminlte::page')

@section('title', 'Detail Payroll')

@section('content_header')
    <h1>Detail Payroll - {{ $periode->translatedFormat('F Y') }}</h1>
@stop

@section('content')

{{-- SUMMARY --}}
<div class="row">

    <div class="col-md-3">
        <div class="info-box bg-info">
            <span class="info-box-text">Total Gaji</span>
            <span class="info-box-number">
                Rp {{ number_format($summary['total_gaji'], 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box bg-success">
            <span class="info-box-text">Total Bonus</span>
            <span class="info-box-number">
                Rp {{ number_format($summary['total_bonus'], 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box bg-warning">
            <span class="info-box-text">Total Potongan</span>
            <span class="info-box-number">
                Rp {{ number_format($summary['total_potongan'], 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box bg-primary">
            <span class="info-box-text">Total Dibayarkan</span>
            <span class="info-box-number">
                Rp {{ number_format($summary['total_dibayar'], 0, ',', '.') }}
            </span>
        </div>
    </div>

</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Karyawan</h3>
    </div>

    <div class="p-0 card-body table-responsive">
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gaji</th>
                    <th>Bonus</th>
                    <th>Potongan</th>
                    <th>Total</th>
                    <th>Hari Kerja</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payrolls as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>

                        <td>
                            {{ $p->user->profile->nama ?? $p->user->username }}
                        </td>

                        <td>Rp {{ number_format($p->gaji, 0, ',', '.') }}</td>

                        <td>Rp {{ number_format($p->bonus, 0, ',', '.') }}</td>

                        <td>Rp {{ number_format($p->potongan, 0, ',', '.') }}</td>

                        <td>
                            <strong>
                                Rp {{ number_format($p->total, 0, ',', '.') }}
                            </strong>
                        </td>

                        <td>{{ $p->hari_kerja }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

<div class="text-right">
    <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
        Kembali
    </a>
</div>

@stop