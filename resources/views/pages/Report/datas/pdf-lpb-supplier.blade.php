<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        font-size: 12px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }

    th {
        background-color: #f0f0f0;
    }

    .group-header {
        background-color: #d3d3d3;
        font-weight: bold;
    }

    .sub-total {
        background-color: #f9f9f9;
        font-weight: bold;
    }

    .grand-total {
        background-color: #333;
        color: #fff;
        font-weight: bold;
    }

    .section-info h4 {
        margin: 0;
        padding: 4px 0;
        font-size: 13px;
    }
</style>

<div class="section-info" style="margin-bottom: 20px;">
    <h4><strong>PEMILIK:</strong> {{ $pemilik }}</h4>
    <h4><strong>TGL KIRIM:</strong> {{ $periode }}</h4>
    <h4><strong>NOPOL:</strong> {{ $nopolResult }}</h4>
</div>

@foreach ($sortedResults as $quality => $lengthGroups)
    <table>
        <thead>
            <tr class="group-header">
                <th colspan="6">{{ strtoupper($quality) }}</th>
            </tr>
            <tr>
                <th>PANJANG</th>
                <th>DIAMETER</th>
                <th>QTY</th>
                <th>M3</th>
                {{-- <th>HARGA/M3</th> --}}
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
                        {{-- <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td> --}}
                        <td>Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                    </tr>

                    @php
                        $totalQty += $item['qty'];
                        $totalM3 += $item['m3'];
                        $totalNilai += $item['nilai'];
                    @endphp
                @endforeach

                <tr class="sub-total">
                    <td>Total {{ $length }}</td>
                    <td>{{ array_sum(array_column($lengthGroups[$length], 'qty')) }}</td>
                    <td>{{ number_format(array_sum(array_column($lengthGroups[$length], 'm3')), 4, ',', '.') }}</td>
                    <td></td>
                    <td>Rp {{ number_format(array_sum(array_column($lengthGroups[$length], 'nilai')), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="sub-total">
                <td>{{ strtoupper($quality) }} Total</td>
                <td>{{ $totalQty }}</td>
                <td>{{ number_format($totalM3, 4, ',', '.') }}</td>
                <td></td>
                <td>Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
@endforeach

{{-- Grand Total Section --}}
<table>
    <thead>
        <tr class="group-header">
            <th>QTY</th>
            <th>KUBIKASI</th>
            <th>PPH22</th>
            <th>TOTAL TRANSFER</th>
        </tr>
    </thead>
    <tfoot>
        <tr class="grand-total">
            <td>{{ $grandTotalQty }}</td>
            <td>{{ number_format($grandTotalM3, 4, ',', '.') }}</td>
            <td>Rp {{ number_format($grandTotalPph, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($grandTotalNilai - $grandTotalPph, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
