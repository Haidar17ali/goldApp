<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print QR Label 84x24</title>

    <style>
        /* ================= PAGE ================= */
        @page {
            size: 84mm 24mm;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ================= ROW (1 BARIS CETAK) ================= */
        .row {
            width: 84mm;
            height: 24mm;
            display: grid;
            grid-template-columns: 42mm 42mm;
            box-sizing: border-box;
        }

        /* ================= LABEL ================= */
        .label {
            height: 24mm;
            display: flex;
            align-items: flex-start;
            /* 🔥 NAIK KE ATAS */
            padding-top: 1.2mm;
            /* 🔥 KOMPENSASI DEAD ZONE */
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ================= SAFE AREA ================= */
        .label.left {
            padding-left: 2mm;
            padding-right: 1mm;
        }

        .label.right {
            padding-right: 2mm;
            padding-left: 1mm;
            flex-direction: row-reverse;
            /* 🔥 MIRROR */
            text-align: right;
        }

        /* ================= QR ================= */
        .qr-box {
            width: 10mm;
            /* 🔥 PALING AMAN */
            height: 10mm;
            flex-shrink: 0;
        }

        .qr-box svg,
        .qr-box img {
            width: 100% !important;
            height: 100% !important;
            shape-rendering: crispEdges;
            /* 🔥 ANTI BLUR */
        }

        /* ================= INFO ================= */
        .info {
            font-size: 7pt;
            line-height: 1.2;
            white-space: nowrap;
        }

        .left .info {
            margin-left: 2mm;
        }

        .right .info {
            margin-right: 2mm;
        }

        .product {
            font-weight: bold;
            text-transform: uppercase;
        }

        .detail {
            font-size: 6.5pt;
        }

        /* ================= PRINT ================= */
        @media print {
            .row {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>

    @php
        $pairs = ceil($qty / 2);
    @endphp

    @for ($i = 0; $i < $pairs; $i++)
        <div class="row">

            {{-- LABEL KIRI --}}
            @if ($i * 2 < $qty)
                <div class="label left">
                    <div class="qr-box" id="qr-{{ $i * 2 }}"></div>
                    <div class="info">
                        <div class="product">{{ strtoupper($item->product->name) }}</div>
                        <div class="detail">{{ $item->karat?->name }} | {{ $item->gram }}g</div>
                        <div class="detail">{{ $item->barcode }}</div>
                    </div>
                </div>
            @else
                <div></div>
            @endif

            {{-- LABEL KANAN (MIRROR) --}}
            @if ($i * 2 + 1 < $qty)
                <div class="label right">
                    <div class="qr-box" id="qr-{{ $i * 2 + 1 }}"></div>
                    <div class="info">
                        <div class="product">{{ strtoupper($item->product->name) }}</div>
                        <div class="detail">{{ $item->karat?->name }} | {{ $item->gram }}g</div>
                        <div class="detail">{{ $item->barcode }}</div>
                    </div>
                </div>
            @else
                <div></div>
            @endif

        </div>
    @endfor

    <!-- ================= QR GENERATOR ================= -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        @for ($i = 0; $i < $qty; $i++)
            new QRCode(document.getElementById("qr-{{ $i }}"), {
                text: "{{ $item->barcode }}",
                width: 40, // 🔥 GENAP = TAJAM (≈10mm @203dpi)
                height: 40,
                useSVG: true, // 🔥 WAJIB
                correctLevel: QRCode.CorrectLevel.L // 🔥 PALING AMAN UNTUK LABEL KECIL
            });
        @endfor

        window.onload = () => window.print();
    </script>

</body>

</html>
