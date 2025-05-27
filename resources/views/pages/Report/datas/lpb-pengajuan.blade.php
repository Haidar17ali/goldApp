<div id="print-preview">
    @foreach ($grouped as $nopol => $lpbs)
        <div class="print-section" style="page-break-after: always;">
            <h4>Nopol: {{ $nopol }}</h4>

            @php
                $grandTotalNopol = 0;
                $grandTotalQty = 0;
                $grandTotalKubikasi = 0;
            @endphp

            @foreach ($lpbs as $lpb)
                <h5>No Kitir: {{ $lpb->no_kitir }} | Tanggal: {{ date('d-m-Y', strtotime($lpb->date)) }}</h5>
                @php
                    $qualityLengthGroups = $lpb->details->groupBy(function ($item) {
                        return $item->quality . '-' . $item->length;
                    });
                    $totalLpb = 0;
                    $totalLpbQty = 0;
                    $totalLpbKubikasi = 0;
                @endphp
                @foreach ($qualityLengthGroups as $groupKey => $items)
                    @php
                        [$quality, $length] = explode('-', $groupKey);
                    @endphp
                    <h6 class="mt-3">Kualitas: {{ $quality }} | Panjang: {{ $length }}</h6>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Diameter</th>
                                <th>Qty</th>
                                <th>Kubikasi</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuality = 0;
                                $totalQty = 0;
                                $totalKubikasi = 0;
                            @endphp
                            @foreach ($items as $detail)
                                @php
                                    $kubikasi = kubikasi($detail->diameter, $detail->length, $detail->qty);
                                    $subtotal = $kubikasi * $detail->price;
                                    $totalQuality += $subtotal;
                                    $totalQty += $detail->qty;
                                    $totalKubikasi += $kubikasi;
                                @endphp
                                <tr>
                                    <td>{{ $detail->diameter }}</td>
                                    <td>{{ $detail->qty }}</td>
                                    <td>{{ number_format($kubikasi, 3, ',', '.') }}</td>
                                    <td>Rp.{{ money_format($detail->price) }}</td>
                                    <td>Rp.{{ money_format($subtotal) }}</td>
                                </tr>
                            @endforeach
                            @php
                                $totalLpb += $totalQuality;
                                $totalLpbQty += $totalQty;
                                $totalLpbKubikasi += $totalKubikasi;
                            @endphp
                            <tr>
                                <td class="text-end" colspan="1"><strong>Total</strong></td>
                                <td><strong>{{ $totalQty }}</strong></td>
                                <td><strong>{{ number_format($totalKubikasi, 3, ',', '.') }}</strong></td>
                                <td colspan="2"><strong>Rp.{{ money_format($totalQuality) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
                {{-- <table border="1" width="100%">
                    <thead>
                        <tr>
                            <th>Length</th>
                            <th>Diameter</th>
                            <th>Qty</th>
                            <th>Kubikasi</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalHarga = 0;
                            $totalKubikasi = 0;
                        @endphp
                        @foreach ($lpb->details as $detail)
                            @php
                                $totalQty += $detail->qty;
                                $totalKubikasi += kubikasi($detail->diameter, $detail->length, $detail->qty);
                                $totalHarga +=
                                    kubikasi($detail->diameter, $detail->length, $detail->qty) * $detail->price;

                                $grandQty += $detail->qty;
                                $grandKubikasi += kubikasi($detail->diameter, $detail->length, $detail->qty);
                                $grandTotal +=
                                    kubikasi($detail->diameter, $detail->length, $detail->qty) * $detail->price;
                            @endphp
                            <tr>
                                <td>{{ $detail->length }}</td>
                                <td>{{ $detail->diameter }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>{{ kubikasi($detail->diameter, $detail->length, $detail->qty) }}</td>
                                <td>Rp.{{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td>Rp.{{ number_format(kubikasi($detail->diameter, $detail->length, $detail->qty) * $detail->price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2">TOTAL</td>
                            <td>{{ $totalQty }}</td>
                            <td>{{ $totalKubikasi }}</td>
                            <td colspan="2">{{ number_format($totalHarga, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table> --}}
                <table class="table table-bordered table-sm mt-3" style="width: 300px; float: right;">
                    <tr class="table-light">
                        <td class="text-right"><strong>Total LPB:</strong></td>
                        <td class="text-right"><strong>{{ money_format($totalLpb) }}</strong></td>
                    </tr>
                </table>
                <div class="clearfix"></div>
                <hr>
                @php
                    $grandTotalNopol += $totalLpb;
                    $grandTotalQty += $totalLpbQty;
                    $grandTotalKubikasi += $totalLpbKubikasi;
                @endphp
                <br>
            @endforeach
            <div class="d-flex justify-content-end mt-4 mb-5">
                <table class="table table-bordered grant-total-table">
                    <thead style="color: #000;">
                        <tr>
                            <th colspan="2" class="text-center">GRAND TOTAL NOPOL: {{ $nopol }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 500;">Total Qty</td>
                            <td class="text-end">{{ number_format($grandTotalQty, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500;">Total Kubikasi</td>
                            <td class="text-end">{{ number_format($grandTotalKubikasi, 3, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 500;">Total Uang</td>
                            <td class="text-end">Rp {{ number_format($grandTotalNopol, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="page-break"></div>
            {{-- GRAND TOTAL PER NOPOL --}}
            {{-- <table class="grand-total-table">
                <thead>
                    <tr>
                        <th colspan="2">GRAND TOTAL NOPOL: {{ $nopol }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Qty</td>
                        <td>{{ $grandQty }}</td>
                    </tr>
                    <tr>
                        <td>Total Kubikasi</td>
                        <td>{{ $grandKubikasi }}</td>
                    </tr>
                    <tr>
                        <td>Total Uang</td>
                        <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table> --}}

        </div>
    @endforeach
</div>
<style>
    .print-section {
        page-break-after: always;
        padding: 20px;
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #333;
    }

    .print-section:last-child {
        page-break-after: avoid;
    }

    .lpb-header {
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 2px solid #666;
    }

    .lpb-header h4 {
        margin: 0;
        font-size: 16px;
        color: #000;
    }

    .lpb-header h5 {
        margin: 5px 0 0 0;
        font-size: 14px;
        color: #555;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    table,
    th,
    td {
        border: 1px solid #ccc;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
        padding: 8px;
        text-align: center;
    }

    td {
        padding: 6px;
        text-align: center;
    }

    .total-row {
        font-weight: bold;
        background-color: #e6f7ff;
    }

    .grand-total-table {
        margin-top: 20px;
    }

    .grand-total-table th {
        background-color: #d9ead3;
        color: #000;
    }

    .grand-total-table td {
        background-color: #f6fff2;
    }
</style>
