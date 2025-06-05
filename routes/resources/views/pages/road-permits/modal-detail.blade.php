<?php
$totalKonversi = 0;
$totalQty = 0;
$totalKubikasi = 0;
$totalNilai = 0;
?>
@foreach ($groupedByKitir as $noKitir => $data)
    <?php
    $totalKonversi += $data['lpb']->conversion;
    ?>
    <div class="section mb-4">
        <h5>NO KITIR: {{ $noKitir }}</h5>
        <div class="row mb-2">
            <div class="col-md-6">
                <p class="mb-1"><strong>Tanggal LPB:</strong> {{ date('d-m-Y', strtotime($roadPermit->date)) }}</p>
                <p class="mb-1"><strong>Supplier:</strong> {{ $roadPermit->from }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>No Polisi:</strong> {{ $roadPermit->nopol }}</p>
                {{-- <p class="mb-1"><strong>Periode:</strong> {{ $roadPermit->bulan }}/{{ $roadPermit->tahun }}</p> --}}
            </div>
        </div>

        @foreach ($data['grouped'] as $quality => $categories)
            <h6 class="mt-3">QUALITY: {{ $quality }}</h6>

            @foreach ($categories as $category => $items)
                <h6>{{ $category }}</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
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
                                <td>{{ $item->length }}</td>
                                <td>{{ $item->diameter }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ kubikasi($item->diameter, $item->length, $item->qty) }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>{{ number_format(kubikasi($item->diameter, $item->length, $item->qty) * $item->price, 0, ',', '.') }}
                                </td>
                            </tr>
                            @php
                                $subtotalQty += $item->qty;
                                $subtotalKubikasi += kubikasi($item->diameter, $item->length, $item->qty);
                                $subtotalNilai += kubikasi($item->diameter, $item->length, $item->qty) * $item->price;
                            @endphp
                        @endforeach
                        <tr>
                            <?php
                            $totalQty += $subtotalQty;
                            $totalKubikasi += $subtotalKubikasi;
                            $totalNilai += $subtotalNilai;
                            ?>
                            <td colspan="2" class="text-end">{{ $category }} Total QTY & Kubikasi</td>
                            <td>{{ $subtotalQty }}</td>
                            <td>{{ $subtotalKubikasi }}</td>
                            <td>Total Uang</td>
                            <td>{{ number_format($subtotalNilai, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endforeach
    </div>
@endforeach
<table class="table table-bordered">
    <thead>
        <th>Total Qty</th>
        <th>Total Kubikasi</th>
        <th>Total Nilai</th>
        <th>Total Konversi</th>
        <th>Total Uang</th>
    </thead>
    <tbody>
        <tr>
            <td>{{ $totalQty }}</td>
            <td>{{ $totalKubikasi }}</td>
            <td>Rp.{{ money_format($totalNilai) }}</td>
            <td>Rp.{{ money_format($totalKonversi * 3000) }}</td>
            <td>Rp.{{ money_format($totalNilai - $totalKonversi * 3000) }}</td>
        </tr>
    </tbody>
</table>
