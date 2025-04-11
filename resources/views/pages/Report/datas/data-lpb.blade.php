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
                <th>Nama Rek</th>
                <th>No Rek</th>
                <th>QTY</th>
                <th>Kubikasi</th>
                <th>Konversi</th>
                <th>Nilai</th>
                <th>PPH22</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grantTotalQty = 0;
                $grantTotalKubikasi = 0;
                $grantTotalKonversi = 0;
                $grantTotalNominal = 0;
                $grantTotalPph22 = 0;
                $grantTotal = 0;
            @endphp
            @forelse($datas as $key => $data)
                @php
                    $totalKubikasi = $data->details != '' ? totalKubikasi($data->details) : 0;
                    $nominalKubikasi =
                        $data->details != '' ? nominalKubikasi($data->details) - $data->conversion * 3000 : 0;
                    $pph22 = $nominalKubikasi * 0.0025;
                    $grantTotalQty += $data->details != '' ? $data->details->sum('qty') : 0;
                    $grantTotalKubikasi += $totalKubikasi;
                    $grantTotalKonversi += $data->conversion;
                    $grantTotalNominal += $nominalKubikasi;
                    $grantTotalPph22 += $pph22;
                    $grantTotal += $nominalKubikasi - $pph22;
                @endphp
                <tr>
                    <td>{{ date('d-m-Y', strtotime($data->date)) }}</td>
                    <td>{{ $data->supplier != '' ? $data->supplier->name : 'Supplier Tidak DiTemukan' }}</td>
                    <td>{{ $data->npwp != '' ? $data->npwp->name : 'NPWP Tidak DiTemukan' }}</td>
                    <td>{{ $data->supplier && $data->supplier->bank ? $data->supplier->bank->bank_account : 'Bank Tidak Ditemukan' }}
                    <td>{{ $data->supplier && $data->supplier->bank ? $data->supplier->bank->number_account : 'Bank Tidak Ditemukan' }}
                    </td>
                    <td>{{ $data->details != '' ? $data->details->sum('qty') : 0 }} Btg</td>
                    <td>{{ $totalKubikasi }} M3</td>
                    <td>Rp.{{ money_format($data->conversion * 3000) }}</td>
                    <td>Rp{{ money_format($nominalKubikasi) }}
                    </td>
                    <td>Rp{{ money_format($pph22) }}</td>
                    <td>Rp{{ money_format($nominalKubikasi - $pph22) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center"><strong>Data Tidak Di Temukan</strong></td>
                </tr>
            @endforelse ($datas as $data)
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Grand Total</th>
                <th>{{ $grantTotalQty }}</th>
                <th>{{ $grantTotalKubikasi }}</th>
                <th>Rp.{{ money_format($grantTotalKonversi * 3000) }}</th>
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
