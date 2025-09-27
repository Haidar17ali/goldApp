@extends('adminlte::page')

@section('title', 'Edit Detail Pemotongan')

@section('content_header')
    <h1>Edit Detail Pemotongan</h1>
@stop

@section('content')
    <form action="{{ route('cutting.updateDetail', $cuttingDetail->id) }}" method="POST" id="formRP">
        @csrf
        @method('PATCH')

        <div class="card">
            <div class="card-header">
                <span class="badge badge-primary">Form Edit Detail Pemotongan</span>
            </div>
            <div class="card-body">

                {{-- Error Validation Alert --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h4 class="mb-2">Terjadi Kesalahan:</h4>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group row">
                    <label for="product" class="col-sm-2 col-form-label">Varian Produk</label>
                    <div class="col-sm-4">
                        <select name="product" class="form-control" id="product">
                            @foreach ($product_variants as $product_variant)
                                <option value="{{ $product_variant['id'] }}"
                                    {{ $product_variant['id'] == $cuttingDetail->product_id ? 'selected' : '' }}>
                                    {{ strtoupper($product_variant['product_name']) }}
                                    {{ strtoupper($product_variant['color_name']) }}
                                    ({{ strtoupper($product_variant['size_code']) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label for="qty" class="col-sm-2 col-form-label">QTY</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="qty" name="qty"
                            value="{{ old('qty', $cuttingDetail->qty) }}">
                        <span class="text-danger error-text" id="qty_error"></span>
                    </div>
                </div>

                {{-- <hr>

                <h5 class="mb-3">Detail Produk</h5>
                <div id="example" class="mb-3"></div>
                <input type="hidden" name="details" id="details" value="{{ old('details') }}"> --}}

                <div class="text-right">
                    <a href="{{ route('pengiriman.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan</button>
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
        $('#color').select2({
            theme: "bootstrap-5",
        }).on('select2:open', function() {
            // Fokuskan ke input search
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
            }, 10);
        });
        $('#product').select2({
            theme: "bootstrap-5",
        }).on('select2:open', function() {
            // Fokuskan ke input search
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
            }, 10);
        });
    </script>
    {{-- <script>
        const cuttingDetails = @json($cuttingDetails);

        function createAutocompleteColumn(sourceData) {
            return {
                type: 'autocomplete',
                source: sourceData.map(item => item.cutting_date + " | " + item.color_name + " | " + item.product_name +
                    " | " +
                    item.color_name + " | " + item.size_name + " | " + item.source_type + " | " + item.qty + " pcs"),
                strict: true,
                allowInvalid: false,
                renderer: function(instance, td, row, col, prop, value, cellProperties) {

                    let displayValue = '';
                    if (value != null) {
                        const item = sourceData.find(d => d.id == value);
                        if (item) {
                            displayValue = item.cutting_date + " | " + item.color_name + " | " + item.product_name +
                                " | " +
                                item.color_name + " | " + item.size_name + " | " + item.source_type + " | " + item.qty +
                                " pcs".toUpperCase();
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

        // Ambil data lama dari Laravel (old input atau dari DB)
        let oldDetails = @json(old('details') ? json_decode(old('details'), true) : $details);

        // Fallback jika kosong
        if (!oldDetails || oldDetails.length === 0) {
            oldDetails = [{
                cuttingDetail: null,
                qty: 0
            }];
        }


        const container = document.getElementById('example');
        const hot = new Handsontable(container, {
            data: oldDetails,
            colHeaders: ['Potongan', 'Jumlah'],
            columns: [{
                    data: 'cuttingDetail',
                    ...createAutocompleteColumn(cuttingDetails)
                },
                {
                    data: 'qty',
                    type: 'numeric',
                    renderer: Handsontable.renderers.NumericRenderer // ✅ pakai renderer numeric bawaan
                }
            ],
            rowHeaders: true,
            autoRowSize: true,
            height: 280,
            minSpareRows: 1,
            width: '100%',
            stretchH: 'all',
            licenseKey: 'non-commercial-and-evaluation'
        });

        // Convert name → id setelah edit
        hot.addHook('afterChange', function(changes, source) {
            if (source === 'edit') {
                changes.forEach(([row, prop, oldVal, newVal]) => {

                    if (!newVal) return;

                    let lookup = null;
                    if (prop === 'cuttingDetail') lookup = cuttingDetails.find(d => d.cutting_date + " | " +
                        d.color_name + " | " + d.product_name +
                        " | " +
                        d.color_name + " | " + d.size_name + " | " + d.source_type + " | " + d.qty +
                        " pcs" === newVal);

                    if (lookup) {

                        hot.setDataAtRowProp(row, prop, lookup.id, 'autoconvert');
                    }
                });
            }
        });

        // Sebelum submit → convert data ke id
        // document.getElementById("formRP").addEventListener("click", function() {
        //     let data = hot.getSourceData();

        //     data = data.map(row => {
        //         let cuttingDetail = cuttingDetails.find(d => d.cutting_date + " | " +
        //             d.color_name + " | " + d.product_name +
        //             " | " +
        //             d.color_name + " | " + d.size_name + " | " + d.source_type + " | " + d.qty +
        //             " pcs" === String(row
        //                 .cuttingDetail) || d.id == row.cuttingDetail);

        //         return {
        //             cuttingDetail: cuttingDetail ? cuttingDetail.id : null,
        //             qty: row.qty ?? 0,
        //             sourceType: cuttingDetail ? cuttingDetail.source_type : null,
        //         };
        //     });
        //     web


        //     // Hapus baris kosong
        //     data = data.filter(row => row.cuttingDetail || row.qty);

        //     document.getElementById("details").value = JSON.stringify(data);
        // });
    </script> --}}
    <script>
        @section('plugins.Toast', true)
            document.addEventListener("DOMContentLoaded", function() {
                @if (session('status') === 'minus-qty')
                    Toastify({
                        text: "Qty yang dikeluarkan melebihi stok (stok minus).",
                        className: "danger",
                        close: true,
                        style: {
                            background: "red",
                        }
                    }).showToast();
                @endif

                @if (session('status') === 'saved')
                    Toastify({
                        text: "Data pengiriman berhasil disimpan.",
                        className: "success",
                        close: true,
                        style: {
                            background: "green",
                        }
                    }).showToast();
                @endif
            });
    </script>
@stop
