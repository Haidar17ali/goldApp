<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Nota Penjualan A5 Landscape</title>

    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }

        html,
        body {
            width: 21cm;
            height: 14cm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            position: relative;
        }

        /* ================= */
        /* INFO KANAN ATAS */
        /* ================= */
        .info {
            position: absolute;
            top: 1.7cm;
            left: 23.8cm;
            width: 4.5cm;
            font-size: 0.38cm;
            line-height: 1.15;
        }

        .info .row {
            margin-bottom: 0.5cm;
        }

        /* ================= */
        /* HEADER TABEL */
        /* ================= */
        .table-header {
            position: absolute;
            top: 5.3cm;
            left: 1cm;
            display: flex;
            font-size: 0.36cm;
            font-weight: bold;
        }

        /* ================= */
        /* KOLOM TABEL */
        /* ================= */
        .col-name {
            width: 7.5cm;
        }

        .col-weight {
            width: 5.5cm;
            text-align: center;
        }

        .col-karat {
            width: 4.2cm;
            text-align: center;
        }

        .col-price {
            width: 4.7cm;
            text-align: center;
        }

        /* ================= */
        /* AREA ITEM */
        /* ================= */
        .items {
            position: absolute;
            top: 7.9cm;
            left: 8cm;
            width: 21cm;
            height: 7.2cm;
        }

        .item-row {
            display: flex;
            font-size: 0.5cm;
            height: 0.9cm;
            align-items: center;
        }

        /* ================= */
        /* TOTAL */
        /* ================= */
        .total {
            position: absolute;
            width: 100%;
            top: 17.2cm;
            left: 25.6cm;
            font-size: 0.5cm;
            font-weight: bold;
        }

        .main-photo {
            position: absolute;
            top: 2cm;
            /* sesuaikan posisi */
            left: -6cm;
            /* posisi kiri */
            width: 5cm;
            /* UKURAN JELAS */
            height: 5cm;
            object-fit: cover;
            border: 0.05cm solid #000;
        }


        /* PRINT */
        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- INFO -->
    <div class="info">
        <div class="row">{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</div>
        <div class="row">{{ $transaction->customer_name }}</div>
        <div class="row">{{ $transaction->address }}</div>
        <div class="row">{{ $transaction->note }}</div>
    </div>

    <!-- HEADER -->
    <div class="table-header">
        {{-- <div class="col-name">Perhiasan Emas</div>
        <div class="col-weight">Berat</div>
        <div class="col-karat">Kadar</div>
        <div class="col-price">Harga</div> --}}
    </div>

    <!-- ITEMS -->
    <div class="items">
        <!-- FOTO BARANG (SATU KALI) -->
        @if ($transaction->photo)
            <img src="{{ asset($transaction->photo) }}" class="main-photo">
        @endif

        @foreach ($transaction->details as $detail)
            <div class="item-row">
                <div class="product-image"></div>
                <div class="col-name">{{ $detail->product->name }}</div>
                <div class="col-weight">{{ $detail->gram }} g</div>
                <div class="col-karat">{{ $detail->karat->name }}</div>
                <div class="col-price">Rp {{ number_format($detail->unit_price, 0, ',', '.') }}</div>
            </div>
            <hr width="100%">
        @endforeach
    </div>

    <!-- TOTAL -->
    <div class="total">
        Rp {{ number_format($transaction->total, 0, ',', '.') }}
    </div>

    <button onclick="window.print()"
        style="
position:absolute;
top:0.4cm;
right:0.4cm;
padding:0.25cm 0.6cm;
font-size:0.35cm;
">
        🖨 Cetak
    </button>

</body>

</html>
