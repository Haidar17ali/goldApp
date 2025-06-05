<div class="row mb-4">
    <div class="col-md-6">
        <p class="mb-1"><strong>SUPLIER:</strong>
            {{ $roadPermit->from }}</p>
        <p class="mb-1"><strong>NOPOL:</strong> {{ $roadPermit->nopol }}</p>
    </div>
</div>

@php
    $totalQtyAfkir = 0;
    $totalQty130 = 0;
    $totalQty260 = 0;
    $totalKubikasiAfkir = 0;
    $totalKubikasi130 = 0;
    $totalKubikasi260 = 0;
    $totalNilaiAfkir = 0;
    $totalNilai130 = 0;
    $totalNilai260 = 0;
@endphp

@foreach ($grouped as $category => $items)
    <div class="section">
        <h5>{{ $roadPermit->code }}</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kualitas</th>
                    <th>Panjang</th>
                    <th>Diameter</th>
                    <th>Qty</th>
                    <th>Kubikasi</th>
                    <th>Harga</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotalQty = 0;
                    $subtotalKubikasi = 0;
                    $subtotalNilai = 0;
                @endphp

                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->quality }}</td>
                        <td>{{ $item->length }}</td>
                        <td>{{ $item->diameter }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ kubikasi($item->diameter, $item->length, $item->qty) }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ number_format(kubikasi($item->diameter, $item->length, $item->qty) * $item->price, 0, ',', '.') }}
                    </tr>
                    @php
                        $subtotalQty += $item->qty;
                        $subtotalKubikasi += kubikasi($item->diameter, $item->length, $item->qty);
                        $subtotalNilai += kubikasi($item->diameter, $item->length, $item->qty) * $item->price;

                        if ($item->length == '130' && $item->quality == 'Afkir') {
                            $totalQtyAfkir += $item->qty;
                            $totalKubikasiAfkir += kubikasi($item->diameter, $item->length, $item->qty);
                            $totalNilaiAfkir += kubikasi($item->diameter, $item->length, $item->qty) * $item->price;
                        } elseif ($item->length == '130' && $item->quality == 'Super') {
                            $totalQty130 += $item->qty;
                            $totalKubikasi130 += kubikasi($item->diameter, $item->length, $item->qty);
                            $totalNilai130 += kubikasi($item->diameter, $item->length, $item->qty) * $item->price;
                        } else {
                            $totalQty260 += $item->qty;
                            $totalKubikasi260 += kubikasi($item->diameter, $item->length, $item->qty);
                            $totalNilai260 += kubikasi($item->diameter, $item->length, $item->qty) * $item->price;
                        }
                    @endphp
                @endforeach

                <tr class="fw-bold">
                    <td colspan="3" class="text-end">{{ $category }} Total</td>
                    <td>{{ $subtotalQty }}</td>
                    <td>{{ $subtotalKubikasi }}</td>
                    <td>Total Nilai</td>
                    <td>{{ number_format($subtotalNilai, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endforeach

<div class="section mt-4">
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>Kualitas</td>
                <td>Qty</td>
                <td>Kubikasi</td>
                <td>Nilai</td>
            </tr>
        </thead>
        <tbody>
            <tr class="fw-bold bg-light">
                <td>Afkir</td>
                <td>{{ $totalQtyAfkir }}</td>
                <td>{{ $totalKubikasiAfkir }}</td>
                <td>{{ money_format($totalNilaiAfkir) }}</td>
            </tr>
            <tr class="fw-bold bg-light">
                <td>130</td>
                <td>{{ $totalQty130 }}</td>
                <td>{{ $totalKubikasi130 }}</td>
                <td>{{ money_format($totalNilai130) }}</td>
            </tr>
            <tr class="fw-bold bg-light">
                <td>260</td>
                <td>{{ $totalQty260 }}</td>
                <td>{{ $totalKubikasi260 }}</td>
                <td>{{ money_format($totalNilai260) }}</td>
            </tr>
            <tr class="fw-bold bg-light">
                <td>Total</td>
                <td>{{ $totalQtyAfkir + $totalQty130 + $totalQty260 }}</td>
                <td>{{ $totalKubikasiAfkir + $totalKubikasi130 + $totalKubikasi260 }}</td>
                <td>{{ money_format($totalNilaiAfkir + $totalNilai130 + $totalNilai260) }}</td>
            </tr>
        </tbody>
    </table>
</div>
