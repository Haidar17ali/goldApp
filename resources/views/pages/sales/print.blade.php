<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Penjualan</title>
    <style>
        @page {
            size: 21cm 11cm;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 21cm;
            height: 11cm;
            font-family: 'Arial', sans-serif;
        }

        body {
            /* background sebagai gambar nota fisik */
            background: url('{{ asset('assets/images/nota.jpg') }}') no-repeat left top;
            background-size: 21cm 11cm;
            /* pastikan ukurannya 100% halaman */
        }

        .overlay {
            position: relative;
            margin-top: 12px;
            width: 21cm;
            height: 11cm;
        }

        /* Foto pelanggan / barang (satu foto saja) */
        .photo {
            position: absolute;
            top: 5cm;
            /* sesuaikan posisi Y foto */
            left: 0.8cm;
            /* sesuaikan posisi X foto */
            width: 3cm;
            /* ukuran foto */
            height: 3cm;
            object-fit: cover;
            border: 0.02cm solid rgba(0, 0, 0, 0.1);
        }

        /* Area alamat & nama di kanan atas (sesuaikan) */
        .info {
            position: absolute;
            top: 0.5cm;
            /* posisi area info */
            left: 16.4cm;
            /* posisi X area info */
            width: 6.5cm;
            font-size: 0.38cm;
            /* ukuran font (cm untuk stabil di print) */
            line-height: 1.1;
        }

        .info .label {
            font-weight: bold !important;
            margin-top: 0.1cm;
            margin-left: 3px;
            text-align: left;
        }

        /* Note di bawah alamat */
        .note {
            position: absolute;
            top: 3cm;
            /* jarak dari atas untuk area note */
            left: 16.3cm;
            width: 6.5cm;
            font-size: 0.33cm;
            line-height: 1.05;
        }

        .note-label {
            position: absolute;
            top: 3cm;
            /* jarak dari atas untuk area note */
            left: 14cm;
            color: #004aad;
            width: 6.5cm;
            font-size: 0.33cm;
            line-height: 1.05;
        }

        .note-double-dot {
            position: absolute;
            top: 3cm;
            /* jarak dari atas untuk area note */
            left: 16cm;
            color: #004aad;
            width: 6.5cm;
            font-size: 0.33cm;
            line-height: 1.05;
        }

        /* Area tabel / daftar produk: gunakan posisi absolut dan perbaris dihitung */
        .items-area {
            position: absolute;
            top: 4.5cm;
            /* posisi awal baris pertama */
            left: -1cm;
            /* posisi kolom paling kiri (setelah foto) */
            width: 18.6cm;
            /* lebar area item */
            /* height tidak perlu ditentukan — tapi kamu harus batasi jumlah baris */
        }

        /* Per baris item menggunakan kelas .item-row, namun top dihitung per baris via inline style */
        .item-row {
            position: absolute;
            left: 0;
            width: 18.6cm;
            font-size: 0.36cm;
            display: flex;
            align-items: center;
        }

        /* lebar kolom — sesuaikan left di dalam .item-row */
        .col-name {
            width: 18.0cm;
            padding-left: 0cm;
            position: relative;
            left: 5cm;
        }

        /* nama produk */
        .col-weight {
            width: 3.0cm;
            text-align: right;
            position: relative;
            left: 1cm;
        }

        .col-karat {
            width: 3cm;
            text-align: right;
            position: relative;
            left: 1cm;
        }

        .col-price {
            width: 2.5cm;
            text-align: right;
            position: relative;
            left: 2.5cm;
            padding-right: 0.2cm;
        }

        .total {
            position: absolute;
            top: 9.2cm;
            /* sesuaikan posisi total */
            left: 17.8cm;
            /* sejajar kanan */
            font-size: 0.45cm;
            font-weight: 700;
            text-align: right;
        }

        /* fallback agar teks tidak pecah */
        .nowrap {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media print {
            button {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="overlay">
        {{-- FOTO: hanya 1 foto --}}
        @if ($transaction->photo)
            <img src="{{ asset($transaction->photo) }}" class="photo" alt="Foto Barang">
        @endif

        {{-- INFO KIRI/ATAS (nama, tanggal, alamat ringkas) --}}
        <div class="info">
            <div class="label">
                {{ \Carbon\Carbon::parse($transaction->date ?? now())->format('d-m-Y') }}</div>
            <div class="label"> {{ $transaction->customer_name }}</div>
            <div class="label"> {{ $transaction->address }}</div>
        </div>
        {{-- NOTE di bawah alamat --}}
        <span class="note-label">Catatan</span>
        <span class="note-double-dot">:</span>
        <div class="note">
            {!! nl2br(e($transaction->note ?? '-')) !!}
        </div>


        {{-- DAFTAR ITEM: per baris dihitung top secara dinamis --}}
        @php
            // Konfigurasi: posisi awal top (cm) dan tinggi tiap baris (cm).
            $startTopCm = 0.6; // top untuk baris pertama (sama dengan .items-area top)
            $rowHeightCm = 0.7; // tinggi tiap baris; sesuaikan agar pas di kotak
            // batasi baris supaya tidak keluar kotak nota
            $maxRows = 8;
        @endphp

        <div class="items-area">
            @foreach ($transaction->details as $detail)
                @if ($loop->index >= $maxRows)
                    {{-- jika lebih dari maxRows, kamu bisa membuat halaman/nota baru atau trim --}}
                    @break
                @endif

                @php
                    $top = $startTopCm + $loop->index * $rowHeightCm; // dalam cm
                @endphp

                <div class="item-row" style="top: {{ $top }}cm;">
                    <div class="col-name nowrap">{{ strtoupper($detail->product?->name) ?? '-' }} {{ $detail->karat?->name ?? '-' }} :</div>
                    <div class="col-weight">{{ $detail->gram ?? '-' }}g</div>
                    <div class="col-karat">{{ $detail->karat?->name ?? '-' }}</div>
                    <div class="col-price">Rp.{{ number_format($detail->unit_price ?? 0, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>

        {{-- TOTAL (di bawah area item, kanan) --}}
        <div class="total">
            Rp. {{ number_format($transaction->total ?? 0, 0, ',', '.') }}
        </div>
    </div>
    <div class="print-button">
        <!-- Konten nota di sini -->

        <button onclick="window.print()"
            style="display:block;: absolute; top: 0.3cm; right: 0.3cm;
               background: #004aad; color: white; border: none;
               padding: 0.25cm 0.7cm; font-size: 0.4cm;
               border-radius: 0.2cm; cursor: pointer;
               box-shadow: 0 0 4px rgba(0,0,0,0.3); z-index: 999; float:right;">
            🖨️ Cetak Nota
        </button>
    </div>



</body>

</html>
