<div class="mb-2 text-left">
    <strong>No Invoice:</strong> {{ $transaction->invoice_number }} <br>
    <strong>Tanggal:</strong> {{ $transaction->transaction_date }} <br>
    <strong>Pelanggan:</strong> {{ $transaction->customer?->name ?? '-' }} <br>
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
                <td>{{ $detail->productVariant?->product->name ?? '-' }}</td>
                <td>{{ $detail->productVariant?->karat->name ?? '-' }}</td>
                <td>{{ $detail->productVariant?->gram ?? 0 }} g</td>
                <td>{{ money_format($detail->unit_price) }}</td>
                <td>{{ $detail->note }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="text-right">
    <strong>Total:</strong> {{ money_format($transaction->total) }}
</div>
