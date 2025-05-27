<div class="container" id="lpb-report-content">
    <div class="laporan-title">
        <h2>LAPORAN LPB</h2>

        <p>PERIODE {{ $periode }}</p>
    </div>
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

                        foreach ($items as $data) {
                            $nominalKubikasi =
                                $data->details != '' ? nominalKubikasi($data->details) - $data->conversion * 3000 : 0;
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
                $grantTotalNominal = 0;
                $grantTotalPph22 = 0;
                $grantTotal = 0;
            @endphp

            @forelse($sorted as $supplierName => $group)
                @php
                    $items = $group['items'];
                    $supplierTotalQty = 0;
                    $supplierTotalKubikasi = 0;
                    // $supplierTotalKonversi = 0;
                    $supplierTotalNominal = 0;
                    $supplierTotalPph22 = 0;
                    $supplierTotal = 0;
                @endphp

                @foreach ($items as $data)
                    @php
                        $totalKubikasi = $data->details != '' ? totalKubikasi($data->details) : 0;
                        $nominalKubikasi =
                            $data->details != '' ? nominalKubikasi($data->details) - $data->conversion * 3000 : 0;
                        $pph22 = $nominalKubikasi * 0.0025;

                        $supplierTotalQty += $data->details != '' ? $data->details->sum('qty') : 0;
                        $supplierTotalKubikasi += $totalKubikasi;
                        // $supplierTotalKonversi += $data->conversion;
                        $supplierTotalNominal += $nominalKubikasi;
                        $supplierTotalPph22 += $pph22;
                        $supplierTotal += $nominalKubikasi - $pph22;

                        $grantTotalQty += $data->details != '' ? $data->details->sum('qty') : 0;
                        $grantTotalKubikasi += $totalKubikasi;
                        // $grantTotalKonversi += $data->conversion;
                        $grantTotalNominal += $nominalKubikasi;
                        $grantTotalPph22 += $pph22;
                        $grantTotal += $nominalKubikasi - $pph22;
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
                        {{-- <td>Rp.{{ money_format($data->conversion * 3000) }}</td> --}}
                        <td>Rp{{ money_format($nominalKubikasi) }}</td>
                        <td>Rp{{ money_format($pph22) }}</td>
                        <td>Rp{{ money_format($nominalKubikasi - $pph22) }}</td>
                    </tr>
                @endforeach

                <tr style="font-weight:bold; background-color: #e9ecef;">
                    <td colspan="8" class="text-right">TOTAL {{ strtoupper($supplierName) }}</td>
                    <td>{{ $supplierTotalQty }} Btg</td>
                    <td>{{ $supplierTotalKubikasi }} M3</td>
                    {{-- <td>Rp.{{ money_format($supplierTotalKonversi * 3000) }}</td> --}}
                    <td>Rp{{ money_format($supplierTotalNominal) }}</td>
                    <td>Rp{{ money_format($supplierTotalPph22) }}</td>
                    <td>Rp{{ money_format($supplierTotal) }}</td>
                </tr>
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
                {{-- <th>Rp.{{ money_format($grantTotalKonversi * 3000) }}</th> --}}
                <th>Rp.{{ money_format($grantTotalNominal) }}</th>
                <th>Rp.{{ money_format($grantTotalPph22) }}</th>
                <th>Rp.{{ money_format($grantTotal) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    /* .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    } */

    .laporan-title {
        text-align: center;
        margin-bottom: 1rem;
    }

    .laporan-title h2 {
        margin: 0;
        font-weight: bold;
    }

    .laporan-title p {
        margin: 0;
        font-size: 1rem;
        color: #6c757d;
    }

    /* .table-wrapper {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    } */
</style>
