<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Nota Penjualan A5 Landscape</title>

    <style>
        /* ===================== */
        /* SET KERTAS */
        /* ===================== */
        @page {
            size: A5 landscape;
            margin: 0;
        }

        html,
        body {
            width: 21cm;
            height: 14.8cm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            position: relative;
            font-size: 0.32cm;
        }

        /* ===================== */
        /* INFO TRANSAKSI */
        /* ===================== */
        .info {
            position: absolute;
            top: 4.1cm;
            left: 1.2cm;
            right: 1.2cm;
            display: flex;
            justify-content: space-between;
        }

        .info div {
            width: 32%;
        }

        /* ===================== */
        /* GARIS */
        /* ===================== */
        .line {
            position: absolute;
            top: 4.9cm;
            left: 1cm;
            right: 1cm;
            border-bottom: 0.05cm solid #000;
        }

        /* ===================== */
        /* FOTO */
        /* ===================== */
        .photo {
            position: absolute;
            top: 5.3cm;
            left: 1cm;
            width: 4.5cm;
            height: 4.5cm;
            border: 0.05cm solid #000;
            object-fit: cover;
        }

        /* ===================== */
        /* HEADER TABEL */
        /* ===================== */
        .table-header {
            position: absolute;
            top: 5.3cm;
            left: 6cm;
            right: 1cm;
            display: flex;
            font-weight: bold;
            border-bottom: 0.05cm solid #000;
            padding-bottom: 0.15cm;
        }

        .col-name {
            width: 32%;
        }

        .col-weight {
            width: 16%;
            text-align: center;
        }

        .col-size {
            width: 14%;
            text-align: center;
        }

        .col-karat {
            width: 14%;
            text-align: center;
        }

        .col-price {
            width: 24%;
            text-align: right;
        }

        /* ===================== */
        /* ITEM */
        /* ===================== */
        .items {
            position: absolute;
            top: 6.3cm;
            left: 6cm;
            right: 1cm;
        }

        .item-row {
            display: flex;
            margin-bottom: 0.18cm;
        }

        /* ===================== */
        /* TOTAL */
        /* ===================== */
        .total {
            position: absolute;
            bottom: 2.8cm;
            right: 1cm;
            font-size: 0.45cm;
            font-weight: bold;
        }

        /* ===================== */
        /* cashier */
        /* ===================== */
        .cashier {
            position: absolute;
            bottom: 0;
            right: 1cm;
            font-size: 0.35cm;
            font-weight: bold;
        }

        /* ===================== */
        /* FOOTER */
        /* ===================== */
        .footer {
            position: absolute;
            bottom: 0.7cm;
            left: 1cm;
            right: 1cm;
            font-size: 0.26cm;
        }

        /* ===================== */
        /* PRINT */
        /* ===================== */
        @media print {
            button {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- INFO -->
    <div class="info">
        <div>Tgl : {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</div>
        <div>Nama : {{ $transaction->customer?->name ?? '' }}</div>
        <div>Alamat : {{ $transaction->customer?->address ?? '' }}</div>
    </div>

    <div class="line"></div>

    <!-- FOTO -->
    @if ($transaction->photo)
        <img src="{{ asset($transaction->photo) }}" class="photo">
    @endif

    <!-- HEADER -->
    <div class="table-header">
        <div class="col-name">Perhiasan</div>
        <div class="col-karat">Kadar</div>
        <div class="col-size">Ukuran</div>
        <div class="col-weight">Berat</div>
        <div class="col-size">Harga /Gram</div>
        <div class="col-price">Harga</div>
    </div>

    <!-- ITEMS -->
    <div class="items">
        @foreach ($transaction->details as $detail)
            <div class="item-row">
                <div class="col-name">{{ ucfirst($detail->productVariant->product->name) }}</div>
                <div class="col-karat">{{ $detail->productVariant->karat->name }}
                    {{ $detail->productVariant->type == 'new' ? ' - N' : '' }}
                </div>
                <div class="col-size"></div>
                <div class="col-weight">{{ $detail->productVariant->gram }} gr</div>
                <div class="col-weight">Rp
                    {{ number_format($detail->unit_price / $detail->productVariant->gram, 0, ',', '.') }}</div>
                <div class="col-price">
                    Rp {{ number_format($detail->unit_price, 0, ',', '.') }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- TOTAL -->
    <div class="total">
        Total : Rp {{ number_format($transaction->total, 0, ',', '.') }}
    </div>

    <div class="cashier">
        Kasir : {{ $transaction->user?->username ?? '' }}
    </div>

    <!-- FOOTER -->
    <!-- <div class="footer">
        * Barang yang sudah dibeli tidak dapat ditukar / dikembalikan <br>
        * Claim garansi wajib membawa nota <br>
        WA : 0812 4964 6542
    </div> -->

    <button onclick="window.print()" style="position:absolute; top:0.4cm; right:0.4cm;">
        🖨 Cetak
    </button>

</body>

</html>
