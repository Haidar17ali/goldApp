@extends('adminlte::page')

@section('title', 'Buat Potongan')

@section('content_header')
    <h1>Buat Potongan</h1>
@stop

@section('content')
    <form action="{{ route('cutting.simpan') }}" method="POST" id="formRP">
        @csrf
        @method('post')

        <div class="card">
            <div class="card-header">
                <span class="badge badge-primary">Form Input Warna</span>
            </div>
            <div class="card-body">

                {{-- Error global --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label">Tanggal</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" id="date" name="date"
                            value="{{ old('date') }}">
                        <span class="text-danger error-text" id="date_error"></span>
                    </div>

                    <label for="name" class="col-sm-2 col-form-label">Nama Penjahit</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name') }}">
                        <span class="text-danger error-text" id="name_error"></span>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Detail Produk</h5>
                <div id="example" class="mb-3"></div>
                <input type="hidden" name="details" id="details" value="{{ old('details') }}">

                <div class="text-right">
                    <a href="{{ route('pengguna.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css" />
    <style>
        #example {
            margin-top: 15px;
        }

        .ht_clone_top th {
            background-color: #3c8dbc;
            color: #fff;
            font-size: 14px;
            text-align: center;
        }

        .ht_master .current {
            background-color: #f0ad4e !important;
        }

        .htCore td,
        .htCore th {
            padding: 6px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            text-align: center;
        }

        .handsontable {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script>
        const products = @json($products);
        const colors = @json($colors);
        const sizes = @json($sizes);

        function createAutocompleteColumn(sourceData) {
            return {
                type: 'autocomplete',
                source: sourceData.map(item => item.name),
                strict: true,
                allowInvalid: false,
                renderer: function(instance, td, row, col, prop, value, cellProperties) {
                    let displayValue = '';
                    if (value != null) {
                        const item = sourceData.find(d => d.id == value);
                        if (item) {
                            displayValue = item.name.toUpperCase();
                        } else {
                            displayValue = value.toString().toUpperCase();
                        }
                    }
                    Handsontable.renderers.TextRenderer.apply(this, [
                        instance, td, row, col, prop, displayValue, cellProperties
                    ]);
                }
            };
        }

        // Ambil data lama dari Laravel (old input)
        let oldDetails = @json(old('details'));

        try {
            if (typeof oldDetails === 'string') {
                oldDetails = JSON.parse(oldDetails);
            }
        } catch (e) {
            oldDetails = [];
        }

        // Fallback jika kosong
        if (!oldDetails || oldDetails.length === 0) {
            oldDetails = [{
                product: null,
                color: null,
                size: null,
                qty: 0
            }];
        }

        const container = document.getElementById('example');
        const hot = new Handsontable(container, {
            data: oldDetails, // gunakan oldDetails agar data tetap ada setelah validasi gagal
            colHeaders: ['Produk', 'Warna', 'Ukuran', 'Jumlah'],
            columns: [{
                    data: 'product',
                    ...createAutocompleteColumn(products)
                },
                {
                    data: 'color',
                    ...createAutocompleteColumn(colors)
                },
                {
                    data: 'size',
                    ...createAutocompleteColumn(sizes)
                },
                {
                    data: 'qty',
                    type: 'numeric'
                }
            ],
            rowHeaders: true,
            minSpareRows: 1,
            width: '100%',
            stretchH: 'all',
            height: 140,
            licenseKey: 'non-commercial-and-evaluation'
        });

        // Convert name → id setelah edit
        hot.addHook('afterChange', function(changes, source) {
            if (source === 'edit') {
                changes.forEach(([row, prop, oldVal, newVal]) => {
                    if (!newVal) return;

                    let lookup = null;
                    if (prop === 'product') lookup = products.find(d => d.name === newVal);
                    if (prop === 'color') lookup = colors.find(d => d.name === newVal);
                    if (prop === 'size') lookup = sizes.find(d => d.name === newVal);

                    if (lookup) {
                        hot.setDataAtRowProp(row, prop, lookup.id, 'autoconvert');
                    }
                });
            }
        });

        // Sebelum submit → convert data ke id dan simpan ke hidden input
        document.getElementById("formRP").addEventListener("submit", function() {
            let data = hot.getSourceData();

            data = data.map(row => {
                let product = products.find(d => d.name.toUpperCase() === String(row.product)
                .toUpperCase() || d.id == row.product);
                let color = colors.find(d => d.name.toUpperCase() === String(row.color).toUpperCase() || d
                    .id == row.color);
                let size = sizes.find(d => d.name.toUpperCase() === String(row.size).toUpperCase() || d
                    .id == row.size);

                return {
                    product: product ? product.id : null,
                    color: color ? color.id : null,
                    size: size ? size.id : null,
                    qty: row.qty ?? 0
                };
            });

            // Hapus baris kosong
            data = data.filter(row => row.product || row.color || row.size || row.qty);

            document.getElementById("details").value = JSON.stringify(data);
        });
    </script>
@stop
