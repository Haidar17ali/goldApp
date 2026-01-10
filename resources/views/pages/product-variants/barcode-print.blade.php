<!DOCTYPE html>
<html>

<head>
    <title>Print QR Label Yupo</title>
    <style>
        @page {
            size: auto;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* CONTAINER UTAMA */
        .container {
            display: grid;
            grid-template-columns: 22mm 22mm;
            /* 2 label per baris */
            column-gap: 34mm;
            /* jarak horizontal antar label */
            row-gap: 2mm;
            /* jarak vertikal antar label */
            padding: 1mm 0 0 1mm;
            /* posisi top-left */
            justify-content: start;
            align-items: start;
        }

        /* SATU LABEL */
        .label {
            width: 22mm;
            height: 24mm;
            padding: 0.5mm;
            box-sizing: border-box;
            background: white;

            display: flex;
            align-items: flex-start;
            /* top-aligned */
            gap: 0.5mm;
            overflow: hidden;
            border: 0.1mm solid #ddd;
            /* garis bantu tipis */
        }

        /* QR CODE */
        .qr {
            width: 8mm;
            height: 8mm;
            flex-shrink: 0;
        }

        .qr img {
            width: 100% !important;
            height: 100% !important;
        }

        /* TEKS */
        .text {
            font-size: 3.5pt;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* teks dari atas */
        }

        .product {
            font-size: 4pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.2mm;
        }

        @media print {
            body {
                background: none;
            }

            .label {
                border: none;
            }

            .container {
                padding: 0.5mm 0 0 0.5mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="container">
        @for ($i = 0; $i < $qty; $i++)
            <div class="label">
                <div id="qr-{{ $i }}" class="qr"></div>
                <div class="text">
                    <div class="product">{{ strtoupper($item->product->name) }}</div>
                    <div>{{ $item->karat?->name }} | {{ $item->gram }}g</div>
                    <div>Barcode: {{ $item->barcode }}</div>
                </div>
            </div>
        @endfor
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        @for ($i = 0; $i < $qty; $i++)
            new QRCode(document.getElementById("qr-{{ $i }}"), {
                text: "{{ $item->barcode }}",
                width: 32, // QR proporsional 8mm
                height: 32,
                correctLevel: QRCode.CorrectLevel.M
            });
        @endfor
    </script>

</body>

</html>
