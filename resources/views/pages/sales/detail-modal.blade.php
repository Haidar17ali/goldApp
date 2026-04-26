<div class="mb-2 text-left">
    <strong>No Invoice:</strong> {{ $transaction->invoice_number }} <br>
    <strong>Tanggal:</strong> {{ $transaction->transaction_date }} <br>
    <strong>Pelanggan:</strong> {{ $transaction->customer?->name ?? '-' }} <br>
    <strong>No Hp:</strong> {{ $transaction->customer?->phone_number ?? '-' }} <br>
    <strong>Alamat:</strong> {{ $transaction->customer?->address ?? '-' }}
</div>

<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Produk</th>
            <th>Kadar</th>
            <th>Berat (gr)</th>
            <th>Harga</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaction->details as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ ucfirst($detail->productVariant?->product->name)  ?? '-' }}  @if ($transaction->manik_price) Perhiasan @endif</td>
                <td>{{ $detail->productVariant?->karat->name ?? '-' }}</td>
                <td>{{ $detail->productVariant?->gram ?? 0 }} g</td>
                <td>{{ money_format($detail->unit_price) }}</td>
                <td>{{ $detail->note }}</td>
            </tr>
        @endforeach
        
        <!-- MANIK -->
        @if ($transaction->manik_price)
            <tr class="item-row">
                <td class="col-name">#</td>
                <td class="col-name">Manik</td>
                <td class="col-karat"></td>
                <td class="col-size"></td>
                <td class="col-price">
                    Rp {{ number_format($transaction->manik_price, 0, ',', '.') }}
                </td>
                <td class="col-weight"></td>
            </tr>
        @endif
    </tbody>
</table>

<div class="text-right">
    <strong>Total:</strong> {{ money_format($transaction->total) }}
</div>
