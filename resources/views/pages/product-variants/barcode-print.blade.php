<!DOCTYPE html>
<html>

<head>
    <title>Print Barcode Label Yupo</title>

    <style>
        @page {
            size: 84mm auto;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 84mm;
        }

        .container {
            width: 78mm;
            margin: 0 3mm;
        }

        /* ===================== BARIS (2 KOLOM) ===================== */
        .row {
            display: grid;
            grid-template-columns: 24mm 24mm;
            column-gap: 34mm;
            page-break-inside: avoid;
        }

        .cell {
            width: 24mm;
            box-sizing: border-box;
        }

        /* ===================== BARCODE ===================== */
        .barcode {
            height: 14mm;
            /* 🔥 BARCODE LEBIH TINGGI */
            padding: 0 2mm;
            /* QUIET ZONE kiri-kanan */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .barcode svg {
            width: 100%;
            height: 100%;
        }

        /* ===================== INFO ===================== */
        .info {
            height: 10mm;
            /* 🔥 SISA TINGGI LABEL */
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.1;
        }

        .product {
            font-size: 7.5pt;
            /* 🔥 LEBIH BESAR */
            font-weight: bold;
            text-transform: uppercase;
        }

        .detail {
            font-size: 6.8pt;
        }

        @media print {
            * {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="page">
        <div class="container">

            @php
                $pairs = ceil($qty / 2);
                $counter = 0;
            @endphp

            @for ($p = 0; $p < $pairs; $p++)

                <!-- BARCODE ROW -->
                <div class="row">
                    @for ($c = 0; $c < 2; $c++)
                        @if ($counter < $qty)
                            <div class="cell barcode">
                                <svg id="barcode-{{ $counter }}"></svg>
                            </div>
                            @php $counter++; @endphp
                        @else
                            <div class="cell"></div>
                        @endif
                    @endfor
                </div>

                <!-- INFO ROW -->
                <div class="row">
                    @for ($c = 0; $c < 2; $c++)
                        @if ($p * 2 + $c < $qty)
                            <div class="cell info">
                                <div class="product">
                                    {{ strtoupper($item->product->name) }}
                                </div>
                                <div class="detail">
                                    {{ $item->karat?->name }} | {{ $item->gram }}g
                                </div>
                            </div>
                        @else
                            <div class="cell"></div>
                        @endif
                    @endfor
                </div>

            @endfor

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <script>
        @for ($i = 0; $i < $qty; $i++)
            JsBarcode("#barcode-{{ $i }}", "{{ $item->barcode }}", {
                format: "CODE128",
                width: 1.6, // 🔥 PALING AMAN UNTUK THERMAL
                height: 48, // BESAR, TAPI DI-CLIP CSS
                displayValue: false,
                margin: 0
            });
        @endfor
    </script>

</body>

</html>
