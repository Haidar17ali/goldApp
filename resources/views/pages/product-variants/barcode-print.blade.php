<!DOCTYPE html>
<html>

<head>
    <title>Print Barcode Label Yupo</title>

    <style>
        @page {
            size: 78mm auto;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* CONTAINER 2 KOLOM – TOTAL PAS 78mm */
        .container {
            display: grid;
            grid-template-columns: 24mm 24mm;
            column-gap: 30mm;
            /* 24 + 30 + 24 = 78 */
            width: 78mm;
        }

        /* 1 LABEL (24 x 24 mm) */
        .label {
            width: 24mm;
            height: 24mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
        }

        /* BARCODE AREA */
        .barcode {
            width: 24mm;
            height: 12mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode svg {
            display: block;
            width: 100%;
            height: auto;
            /* PENTING */
        }

        /* INFO AREA */
        .info {
            width: 24mm;
            height: 12mm;
            font-size: 4.5pt;
            line-height: 1.2;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product {
            font-size: 5.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="container">
        @for ($i = 0; $i < $qty; $i++)
            <div class="label">

                <div class="barcode">
                    <svg id="barcode-{{ $i }}"></svg>
                </div>

                <div class="info">
                    <div class="product">{{ strtoupper($item->product->name) }}</div>
                    <div>{{ $item->karat?->name }} | {{ $item->gram }}g</div>
                </div>

            </div>
        @endfor
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <script>
        @for ($i = 0; $i < $qty; $i++)
            JsBarcode("#barcode-{{ $i }}", "{{ $item->barcode }}", {
                format: "CODE128",
                width: 1.7, // sweet spot untuk 24mm
                height: 26,
                displayValue: false,
                margin: 0
            });
        @endfor
    </script>

</body>

</html>
