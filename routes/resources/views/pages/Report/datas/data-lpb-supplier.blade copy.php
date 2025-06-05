<div class="mb-4">
    <h4><strong>PEMILIK:</strong> {{ $data['pemilik'] }}</h4>
    <h4><strong>TGL KIRIM:</strong> {{ $data['periode'] }}</h4>
    <h4><strong>NOPOL:</strong> {{ $data['nopolResult'] }}</h4>
</div>

@foreach ($data['sortedResults'] as $quality => $lengthGroups)
    <table class="table table-bordered mb-5">
        <thead class="table-secondary">
            <tr>
                <th colspan="6">{{ strtoupper($quality) }}</th>
            </tr>
            <tr>
                <th>PANJANG</th>
                <th>DIAMETER</th>
                <th>QTY</th>
                <th>M3</th>
                <th>HARGA/M3</th>
                <th>NILAI</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalM3 = 0;
                $totalNilai = 0;
            @endphp

            @foreach ($lengthGroups as $length => $items)
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $length }}</td>
                        <td>{{ $item['diameter'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ number_format($item['m3'], 4, ',', '.') }}</td>
                        <td>Rp.{{ money_format($item['harga']) }}</td>
                        <td>Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                    </tr>

                    @php
                        $totalQty += $item['qty'];
                        $totalM3 += $item['m3'];
                        $totalNilai += $item['nilai'];
                    @endphp
                @endforeach

                <tr class="table-light">
                    <td colspan="2"><strong>Total {{ $length }}</strong></td>
                    <td><strong>{{ array_sum(array_column($lengthGroups[$length], 'qty')) }}</strong></td>
                    <td><strong>{{ number_format(array_sum(array_column($lengthGroups[$length], 'm3')), 4, ',', '.') }}</strong>
                    </td>
                    <td></td>
                    <td><strong>Rp
                            {{ number_format(array_sum(array_column($lengthGroups[$length], 'nilai')), 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <td colspan="2"><strong>{{ strtoupper($quality) }} Total</strong></td>
                <td><strong>{{ $totalQty }}</strong></td>
                <td><strong>{{ number_format($totalM3, 4, ',', '.') }}</strong></td>
                <td></td>
                <td><strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
@endforeach

{{-- Grand Total --}}
<table class="table table-bordered">
    <thead>
        <th>QTY</th>
        <th>Kubikasi</th>
        <th>PPH22</th>
        <th>Total Transfer</th>
    </thead>
    <tfoot class="table-dark text-white">
        <tr>
            <td><strong>{{ $data['grandTotalQty'] }}</strong></td>
            <td><strong>{{ number_format($data['grandTotalM3'], 4, ',', '.') }}</strong></td>
            <td><strong>{{ number_format($data['grandTotalPph'], 0, ',', '.') }}</strong></td>
            <td><strong>Rp {{ number_format($data['grandTotalNilai'] - $data['grandTotalPph'], 0, ',', '.') }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
