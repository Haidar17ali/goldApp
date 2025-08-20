<div class="container" id="lpb-report-content">
    <div class="laporan-title">
        <h2>LAPORAN Penerimaan Barang</h2>

        <p>PERIODE {{ $periode }}</p>
    </div>
    <div class="data-wrapper">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>NPWP</th>
                    <th>No Tally</th>
                    <th>Nopol</th>
                    <th>Nama Rek</th>
                    <th>No Rek</th>
                    <th>Bank</th>
                    <th>QTY</th>
                    <th>Kubikasi</th>
                    <th>Nilai LPB</th>
                    <th>Konversi/Borongan</th>
                    <th>Nilai</th>
                    <th>PPH22</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = $datas->groupBy(function ($item) {
                        return $item->supplier?->name ?? 'Supplier Tidak Ditemukan';
                    });

                    // Hitung total nilai per supplier untuk sorting
                    $sorted = $grouped
                        ->map(function ($items, $supplierName) {
                            $total = 0;

                            foreach ($items as $index => $data) {
                                $nominalKubikasi =
                                    $data->details != '' ? nominalKubikasi($data->details) + $data->conversion : 0;
                                $pph22 = $nominalKubikasi * 0.0025;
                                $total += $nominalKubikasi - $pph22;
                            }

                            return ['items' => $items, 'total' => $total];
                        })
                        ->sortBy('total');
                @endphp
                @php
                    $grantTotalQty = 0;
                    $grantTotalKubikasi = 0;
                    $grantTotalKonversi = 0;
                    $grantTotalNotaKonversi = 0;
                    $grantTotalNominal = 0;
                    $grantTotalPph22 = 0;
                    $grantTotal = 0;
                    $totalSemuaDP = array_sum(array_column($supplierDPRincian, 'total'));
                @endphp

                @forelse($sorted as $supplierName => $group)
                    @php
                        $items = $group['items'];
                        $supplierTotalQty = 0;
                        $supplierTotalKubikasi = 0;
                        $supplierTotalKonversi = 0;
                        $supplierTotalNotaKonversi = 0;
                        $supplierTotalNominal = 0;
                        $supplierTotalPph22 = 0;
                        $supplierTotal = 0;
                        $supplierTotalTf = 0;
                    @endphp

                    @foreach ($items as $data)
                        @php
                            $totalKubikasi = $data->details != '' ? totalKubikasi($data->details) : 0;
                            $nominalKubikasi = $data->details != '' ? nominalKubikasi($data->details) : 0;
                            $result = $nominalKubikasi + $data->conversion + $data->nota_conversion;
                            $pph22 = $result * 0.0025;

                            $supplierTotalQty += $data->details != '' ? $data->details->sum('qty') : 0;
                            $supplierTotalKubikasi += $totalKubikasi;
                            $supplierTotalKonversi += $data->conversion;
                            $supplierTotalNotaKonversi += $data->nota_conversion;
                            $supplierTotalNominal += $nominalKubikasi;
                            $supplierTotalPph22 += $pph22;
                            $supplierTotal += $result - $pph22;
                            if (array_key_exists($items->first()->supplier_id, $supplierDPRincian)) {
                                $supplierTotalTF =
                                    $supplierTotal - $supplierDPRincian[$items->first()->supplier_id]['total'];
                            }

                            $grantTotalQty += $data->details != '' ? $data->details->sum('qty') : 0;
                            $grantTotalKubikasi += $totalKubikasi;
                            $grantTotalKonversi += $data->conversion;
                            $grantTotalNotaKonversi += $data->nota_conversion;
                            $grantTotalNominal += $nominalKubikasi;
                            $grantTotalPph22 += $pph22;
                            $grantTotal += $result - $pph22;
                        @endphp
                        <tr>
                            <td>{{ date('d-m-Y', strtotime($data->date)) }}</td>
                            <td>{{ $supplierName }}</td>
                            <td>{{ $data->npwp?->name ?? 'NPWP Tidak Ditemukan' }}</td>
                            <td>{{ $data->no_kitir }}</td>
                            <td>{{ $data->nopol }}</td>
                            <td>{{ $data->bank_account }}</td>
                            <td>{{ $data->number_account }}</td>
                            <td>{{ $data->bank_name }}</td>
                            <td>{{ $data->details != '' ? $data->details->sum('qty') : 0 }} Btg</td>
                            <td>{{ $totalKubikasi }} M3</td>
                            <td>Rp{{ money_format($nominalKubikasi) }}</td>
                            <td
                                class="text-{{ $data->conversion + $data->nota_conversion == 0 ? 'dark' : ($data->conversion + $data->nota_conversion > 0 ? 'success' : 'danger') }}">
                                Rp.{{ money_format($data->conversion + $data->nota_conversion) }}</td>
                            <td>Rp{{ money_format($result) }}</td>

                            <td class="text-danger">Rp{{ money_format($pph22) }}</td>
                            <td>Rp{{ money_format($result - ceil($pph22)) }}
                            </td>
                        </tr>
                    @endforeach

                    <tr style="font-weight:bold; background-color: #e9ecef;">
                        <td colspan="8" class="text-right">
                            TOTAL {{ strtoupper($items->first()->npwp?->name ?? 'NPWP TIDAK DITEMUKAN') }}
                        </td>
                        <td>{{ $supplierTotalQty }} Btg</td>
                        <td>{{ $supplierTotalKubikasi }} M3</td>
                        <td>Rp.{{ money_format($supplierTotalNominal) }}</td>
                        <td>Rp.{{ money_format($supplierTotalKonversi + $supplierTotalNotaKonversi) }}</td>
                        <td>Rp{{ money_format($supplierTotalNominal + $supplierTotalKonversi + $supplierTotalNotaKonversi) }}
                        </td>
                        <td>Rp{{ money_format($supplierTotalPph22) }}</td>
                        <td>Rp{{ money_format($supplierTotal) }}</td>
                    </tr>

                    @if (isset($supplierDPRincian[$items->first()->supplier_id]) &&
                            $supplierDPRincian[$items->first()->supplier_id]['total'] > 0)
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="14" class="text-center text-bold">Potongan DP
                                {{ strtoupper($items->first()->npwp?->name ?? 'NPWP TIDAK DITEMUKAN') }}</td>
                            <td class="text-danger">
                                Rp.{{ money_format($supplierDPRincian[$items->first()->supplier_id]['total']) }}
                            </td>
                        </tr>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="14" class="text-center text-bold">Total Transfer
                                {{ strtoupper($items->first()->npwp?->name ?? 'NPWP TIDAK DITEMUKAN') }}</td>
                            <td class="text-danger">
                                Rp.{{ money_format($supplierTotal - $supplierDPRincian[$items->first()->supplier_id]['total']) }}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="11" class="text-center"><strong>Data Tidak Di Temukan</strong></td>
                    </tr>
                @endforelse

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-right">Grand Total</th>
                    <th>{{ $grantTotalQty }}</th>
                    <th>{{ $grantTotalKubikasi }}</th>
                    <th>Rp.{{ money_format($grantTotalNominal) }}</th>
                    <th>Rp.{{ money_format($grantTotalKonversi + $grantTotalNotaKonversi) }}</th>
                    <th>Rp.{{ money_format($grantTotalNominal + $grantTotalKonversi - abs($grantTotalNotaKonversi)) }}
                    </th>
                    <th>Rp.{{ money_format($grantTotalPph22) }}</th>
                    <th>Rp.{{ money_format($grantTotal) }}</th>
                </tr>
                <tr>
                    <td colspan="14">Potongan DP</td>
                    <td class="text-danger">Rp.{{ money_format($totalSemuaDP) }}</td>
                </tr>
                <tr>
                    <td colspan="14">DP Baru</td>
                    <td class="text-success">Rp.{{ number_format($down_payments->sum('total_nominal'), 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="14">Total Transfer</td>
                    <td class="text-success">
                        Rp.{{ number_format($grantTotal - $totalSemuaDP + $down_payments->sum('total_nominal'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{-- list dp baru --}}
    <table class="compact-table mt-2">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($down_payments as $item)
                <tr>
                    <td>{{ $item->supplier->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Tidak ada data hari ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($down_payments->count())
            <tfoot>
                <tr>
                    <th>Total Semua</th>
                    <th class="text-end">
                        {{ number_format($down_payments->sum('total_nominal'), 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>


<style>
    .data-wrapper {
        display: flex;
        justify-content: center !important;
    }

    .laporan-title {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .laporan-title h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }

    .laporan-title p {
        margin: 0;
        font-size: 1rem;
        color: #6c757d;
    }

    .table-wrapper {
        background: #fff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        text-align: center;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table thead th {
        white-space: nowrap;
    }

    tfoot td {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    @media screen and (max-width: 768px) {
        .laporan-title h2 {
            font-size: 1.25rem;
        }

        .table th,
        .table td {
            font-size: 0.75rem;
            padding: 0.5rem;
        }
    }
</style>
