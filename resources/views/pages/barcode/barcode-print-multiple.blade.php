<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print QR Label 84x24</title>

    <style>
        @page {
            size: 84mm 13mm;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* ROW */
        .row {
            margin-top: 0;
            width: 84mm;
            height: 23mm;
            display: grid;
            grid-template-columns: 42mm 42mm;
            box-sizing: border-box;
        }

        /* LABEL */
        .label {
            height: 23mm;
            display: flex;
            flex-direction: column;
            /* 🔥 ubah jadi vertikal */
            /* align-items: center; */
            /* 🔥 center horizontal */
            justify-content: center;
            /* 🔥 center vertical */
            box-sizing: border-box;
            /* overflow: hidden; */
        }

        /* SAFE AREA */
        .label.left {
            padding-left: 8mm;
            padding-right: 1mm;
        }

        .label.right {
            padding-left: 25mm;
            padding-right: 3mm;
            text-align: right;
        }

        /* QR */
        .qr-box {
            width: 10mm;
            height: 10mm;
            flex-shrink: 0;
        }

        .qr-box svg,
        .qr-box img {
            width: 100% !important;
            height: 100% !important;
            shape-rendering: crispEdges;
        }

        /* TEXT */
        .info {
            font-size: 6pt;
            /* 🔥 naik dari 5pt */
            line-height: 1.1;
            text-align: left;
            margin-top: 3mm;
            /* 🔥 jarak dari QR */
        }

        .product {
            font-weight: bold;
            font-size: 5.5pt;
            /* 🔥 lebih menonjol */
        }

        .detail-code {
            font-weight: bold;
            font-size: 5.5pt;
            margin-top: 3px;
            /* 🔥 lebih menonjol */
        }

        .detail {
            font-size: 7.5pt;
        }

        .left .info {
            margin-left: 2mm;
        }

        .right .info {
            /* margin-right: 2mm; */
        }

        @media print {
            .row {
                page-break-after: always;
                margin: 0;
            }
        }
    </style>

    {{-- <style>
        @page {
            size: 84mm 13mm;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* 1 ROW = 1 LABEL */
        .row {
            margin-top: -4px;
            width: 84mm;
            height: 13mm;
            display: grid;
            grid-template-columns: 42mm 42mm;
            box-sizing: border-box;
        }

        /* LABEL */
        .label {
            height: 13mm;
            display: flex;
            align-items: center;
            /* 🔥 lebih aman dari flex-start */
            box-sizing: border-box;
            overflow: hidden;
        }

        /* SAFE AREA */
        .label.left {
            padding-left: 4mm;
            padding-right: 1mm;
        }

        .label.right {
            padding-left: 1mm;
            padding-right: 3mm;
            flex-direction: row-reverse;
            text-align: right;
        }

        /* QR */
        .qr-box {
            width: 10mm;
            height: 10mm;
            flex-shrink: 0;
        }

        .qr-box svg,
        .qr-box img {
            width: 100% !important;
            height: 100% !important;
            shape-rendering: crispEdges;
        }

        /* TEXT */
        .info {
            font-size: 5pt;
            line-height: 1.1;
            white-space: nowrap;
        }

        .left .info {
            margin-left: 2mm;
        }

        .right .info {
            margin-right: 2mm;
        }

        .product {
            text-transform: uppercase;
        }

        .detail {
            font-size: 5pt;
        }

        /* ❌ HAPUS page-break */
        @media print {
            .row {
                page-break-after: always;
                margin: 0;
            }
        }
    </style> --}}
</head>

<body>

    @php
        /*
    |--------------------------------------------------------------------------
    | Flatten Semua Variant Jadi Label List
    |--------------------------------------------------------------------------
    */
        $labels = [];

        foreach ($items as $row) {
            for ($i = 0; $i < $row['qty']; $i++) {
                $labels[] = $row['variant'];
            }
        }

        $totalLabels = count($labels);
        $pairs = ceil($totalLabels / 2);
    @endphp


    @for ($i = 0; $i < $pairs; $i++)
        <div class="row">

            {{-- LEFT --}}
            @if (isset($labels[$i * 2]))
                @php $item = $labels[$i*2]; @endphp
                <div class="label left">
                    <div class="qr-box" id="qr-{{ $i * 2 }}"></div>
                    <div class="info">
                        <div class="product">{{ strtoupper($item->product->name) }}</div>
                        <div class="detail">{{ $item->karat?->name }} {{ $item->type == 'new' ? 'N' : '' }} |
                            {{ $item->gram }}g</div>
                        <div class="detail-code">{{ substr($item->barcode, 0, 6) }}</div>
                    </div>
                </div>
            @else
                <div></div>
            @endif


            {{-- RIGHT --}}
            @if (isset($labels[$i * 2 + 1]))
                @php $item = $labels[$i*2+1]; @endphp
                <div class="label right">
                    <div class="qr-box" id="qr-{{ $i * 2 + 1 }}"></div>
                    <div class="info">
                        <div class="product">{{ strtoupper($item->product->name) }}</div>
                        <div class="detail">{{ $item->karat?->name }} {{ $item->type == 'new' ? 'N' : '' }} |
                            {{ $item->gram }}g</div>
                        <div class="detail-code">{{ substr($item->barcode, 0, 6) }}</div>
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
        @for ($i = 0; $i < $totalLabels; $i++)
            new QRCode(document.getElementById("qr-{{ $i }}"), {
                text: "{{ $labels[$i]->barcode }}",
                width: 40,
                height: 40,
                useSVG: true,
                correctLevel: QRCode.CorrectLevel.L
            });
        @endfor

        window.onload = () => window.print();
    </script>

</body>

</html>
