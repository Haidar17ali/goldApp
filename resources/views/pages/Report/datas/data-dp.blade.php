<div class="container-fluid" id="lpb-report-content">
    <div class="laporan-title text-center mb-4">
        <h2 class="fw-bold">LAPORAN DP</h2>
        <p class="text-muted">PERIODE {{ $periode }}</p>
    </div>

    <div class="table-responsive shadow rounded p-3 bg-white">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Supplier</th>
                    <th>NPWP</th>
                    <th>Total DP</th>
                    <th>DP Terpakai</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                    {{-- <th>Tanggal Nota</th>
                    <th>Supplier</th>
                    <th>Nopol</th>
                    <th>Tipe</th>
                    <th>Nominal Nota</th>
                    <th>PPH</th>
                    <th>Nominal</th>
                    <th>DP</th>
                    <th>Pelunasan</th>
                    <th>Tgl DP & Pelunasan</th>
                    <th>No Kitir</th>
                    <th>Nilai LPB</th>
                    <th>Konversi Pot</th>
                    <th>Konversi Nota</th>
                    <th>Nilai LPB Final</th>
                    <th>PPH LPB</th>
                    <th>Nilai Akhir</th>
                    <th>Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($datas as $row)
                    <tr>
                        <td>{{ $row['supplier'] }}</td>
                        <td>{{ $row['npwp'] }}</td>
                        <td>Rp.{{ money_format($row['total_dp']) ?? '-' }}</td>
                        <td>Rp.{{ money_format($row['used_dp']) }}</td>
                        <td>Rp.{{ money_format($row['saldo']) }}</td>
                        <td><a href="{{ route('laporan.dp-detail', $row['supplier_id']) }}" class="badge badge-primary"><i
                                    class="fas fa-eye"></i></a></td>
                    </tr>
                    {{-- <tr>
                        <td>{{ \Carbon\Carbon::parse($row['nota_date'])->format('Y-m-d') }}</td>
                        <td>{{ $row['supplier'] }}</td>
                        <td>{{ $row['nopol'] ?? '-' }}</td>
                        <td>{{ $row['tipe'] }}</td>
                        <td>{{ number_format($row['nominal_nota']) }}</td>
                        <td>{{ number_format($row['pph']) }}</td>
                        <td>{{ number_format($row['nominal']) }}</td>
                        <td>{{ number_format($row['dp_nominal']) }}</td>
                        <td>{{ number_format($row['pelunasan_nominal']) }}</td>
                        <td>{!! $row['dp_pelunasan_dates'] !!}</td>
                        <td>{{ $row['no_kitir'] }}</td>
                        <td>{{ number_format($row['nilai_lpb'] ?? 0) }}</td>
                        <td>{{ number_format($row['konversi_pot'] ?? 0) }}</td>
                        <td>{{ number_format($row['konversi_nota'] ?? 0) }}</td>
                        <td>{{ number_format($row['nilai_lpb_final'] ?? 0) }}</td>
                        <td>{{ number_format($row['pph_lpb'] ?? 0) }}</td>
                        <td>{{ number_format($row['nilai_akhir'] ?? 0) }}</td>
                        <td>
                            @if ($row['status'] == 'grade')
                                <span class="text-success fw-bold">✅ Sudah di-grade</span>
                            @else
                                <span class="text-danger fw-bold">❌ Belum di-grade</span>
                            @endif
                        </td>
                    </tr> --}}
                @empty
                    <tr>
                        <td colspan="18" class="text-center text-muted">Tidak ada data ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    #lpb-report-content {
        padding: 1rem;
    }

    .table {
        font-size: 0.85rem;
    }

    .table thead th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .laporan-title h2 {
        font-size: 1.75rem;
    }

    .laporan-title p {
        font-size: 1rem;
        color: #6c757d;
    }

    @media screen and (max-width: 768px) {
        .table {
            font-size: 0.75rem;
        }

        .laporan-title h2 {
            font-size: 1.25rem;
        }
    }
</style>
