<div class="section-info" style="margin-bottom: 20px;">
    <h4><strong>PEMILIK:</strong> {{ $pemilik }}</h4>
    <h4><strong>TGL KIRIM:</strong> {{ date('d-m-Y', strtotime($periode)) }}</h4>
    <h4><strong>NOPOL:</strong> {{ $nopolResult }}</h4>
</div>

@php
    $totalBayar = 0;
@endphp
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
                @php
                    $totalBayar += array_sum(array_column($lengthGroups[$length], 'nilai'));
                    // dd($lengthGroups);
                @endphp
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $length }}</td>
                        <td>{{ $item['diameter'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ number_format($item['m3'], 4, ',', '.') }}</td>
                        {{-- <td>{{ $item['harga']) }}</td> --}}
                        <td>{{ $item['nilai'] }}</td>
                    </tr>

                    @php
                        $totalQty += $item['qty'];
                        $totalM3 += $item['m3'];
                        $totalNilai += $item['nilai'];
                    @endphp
                @endforeach

                <tr class="sub-total">
                    <td colspan="2">Total {{ $length }}</td>
                    <td>{{ array_sum(array_column($lengthGroups[$length], 'qty')) }}</td>
                    <td>{{ number_format(array_sum(array_column($lengthGroups[$length], 'm3')), 4, ',', '.') }}</td>
                    <td>{{ array_sum(array_column($lengthGroups[$length], 'nilai')) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        {{-- <tfoot>
            <tr class="sub-total">
                <td>{{ strtoupper($quality) }} Total</td>
                <td>{{ $totalQty }}</td>
                <td>{{ number_format($totalM3, 4, ',', '.') }}</td>
                <td></td>
                <td>{{ $totalNilai) }}</td>
            </tr>
        </tfoot> --}}
    </table>
@endforeach

<tr>
    <td colspan="2">Grant Total : </td>
    <td>{{ $grandTotalQty }}</td>
    <td>{{ number_format($grandTotalM3, 4, ',', '.') }}</td>
    <td>{{ $totalBayar }}</td>
</tr>
<tr>
    <td colspan="4">Total PPh</td>
    <td>{{ $grandTotalPph }}</td>
</tr>
<tr>
    <td colspan="4">Total Transfer</td>
    <td>{{ $totalBayar - $grandTotalPph }}</td>
</tr>

{{-- Grand Total Section --}}
{{-- <table>
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
            <td>{{ $grandTotalPph) }}</td>
            <td>{{ $grandTotalNilai - $grandTotalPph) }}</td>
        </tr>
    </tfoot>
</table> --}}
