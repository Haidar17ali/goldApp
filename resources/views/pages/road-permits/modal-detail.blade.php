@foreach ($groupedByKitir as $noKitir => $data)
    <div class="section mb-4">
        <h5>NO KITIR: {{ $noKitir }}</h5>
        <div class="row mb-2">
            <div class="col-md-6">
                <p class="mb-1"><strong>Tanggal LPB:</strong> {{ $data['lpb']->tanggal }}</p>
                <p class="mb-1"><strong>Supplier:</strong> {{ $roadPermit->supplier }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>No Polisi:</strong> {{ $roadPermit->nopol }}</p>
                <p class="mb-1"><strong>Periode:</strong> {{ $roadPermit->bulan }}/{{ $roadPermit->tahun }}</p>
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
                            <th>Harga</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subtotalQty = 0;
                            $subtotalNilai = 0;
                        @endphp
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->length }}</td>
                                <td>{{ $item->diameter }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format($item->price, 4, ',', '.') }}</td>
                                <td>{{ number_format($item->qty * $item->price, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $subtotalQty += $item->qty;
                                $subtotalNilai += $item->qty * $item->price;
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="2" class="text-end">{{ $category }} Total</td>
                            <td>{{ $subtotalQty }}</td>
                            <td></td>
                            <td>{{ number_format($subtotalNilai, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endforeach
    </div>
@endforeach
