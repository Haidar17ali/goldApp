@extends('adminlte::page')

@section('title', 'Tambah Transaksi')

@section('content_header')
    <h1>Tambah Transaksi Pembelian</h1>
@stop

@section('content')
    <div class="shadow card">
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first('msg') }}
                </div>
            @endif
        </div>
        <div class="card-body">
            <form id="transactionForm" method="POST"
                action="{{ route('transaksi.simpan', ['type' => $type, 'purchaseType' => $purchaseType]) }}">
                @csrf

                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label for="invoice_number" class="form-label">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number" class="form-control"
                            value="{{ 'INV-' . strtoupper(Str::random(6)) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="customer_name" class="form-label">Nama Customer</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control"
                            placeholder="Masukkan nama customer" required>
                    </div>
                    <div class="col-md-4">
                        <label for="note" class="form-label">Catatan</label>
                        <input type="text" name="note" id="note" class="form-control"
                            placeholder="Catatan tambahan">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Detail Barang</label>
                    <div id="hot" class="border rounded" style="width:100%; height:600px;"></div>
                    <div class="mt-3 text-right">
                        <h5><strong>Grand Total: Rp <span id="grandTotal">0</span></strong></h5>
                    </div>
                </div>


                <button type="submit" class="float-right btn btn-primary">Simpan Transaksi</button>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css">

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const variants = @json($variants ?? []);
            const variantNames = variants.map(v => v.label);
            const variantMap = {};
            variants.forEach(v => {
                variantMap[v.label] = v;
            });

            const oldDetails = @json(old('details') ? json_decode(old('details'), true) : []);

            const container = document.getElementById('hot');
            const grandTotalElement = document.getElementById('grandTotal');

            function updateGrandTotal(hotInstance) {
                const data = hotInstance.getSourceData();
                let total = 0;
                data.forEach(row => total += parseFloat(row.subtotal) || 0);
                grandTotalElement.textContent = total.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
            }

            const hot = new Handsontable(container, {
                data: oldDetails.length ? oldDetails : [],
                colHeaders: ['Varian', 'Produk', 'Karat', 'Gram', 'Qty', 'Harga/gr', 'Subtotal'],
                columns: [{
                        data: 'variant_label',
                        type: 'autocomplete',
                        source: variantNames,
                        strict: false,
                        allowInvalid: true
                    },
                    {
                        data: 'product_name',
                        type: 'text'
                    }, // ✅ sekarang editable
                    {
                        data: 'karat_name',
                        type: 'text'
                    }, // ✅ sekarang editable
                    {
                        data: 'gram',
                        type: 'numeric',
                        numericFormat: {
                            pattern: '0.000'
                        }
                    }, // ✅ editable juga
                    {
                        data: 'qty',
                        type: 'numeric'
                    },
                    {
                        data: 'price_per_gram',
                        type: 'numeric',
                        numericFormat: {
                            pattern: '0,0.00'
                        }
                    },
                    {
                        data: 'subtotal',
                        type: 'numeric',
                        readOnly: true,
                        numericFormat: {
                            pattern: '0,0.00'
                        }
                    },
                ],
                stretchH: 'all',
                rowHeaders: true,
                height: '260px',
                minSpareRows: 10,
                licenseKey: 'non-commercial-and-evaluation',

                afterChange(changes, source) {
                    if (!changes || source === 'loadData') return;

                    changes.forEach(([row, prop, oldValue, newValue]) => {
                        if (prop === 'variant_label' && newValue) {
                            const v = variantMap[newValue];
                            if (v) {
                                // Auto isi hanya jika varian dikenali
                                this.setDataAtRowProp(row, 'product_name', v.product_name);
                                this.setDataAtRowProp(row, 'karat_name', v.karat_name);
                                this.setDataAtRowProp(row, 'gram', v.gram);
                            }
                        }

                        if (["gram", "qty", "price_per_gram"].includes(prop)) {
                            const rowData = this.getSourceDataAtRow(row);
                            const gram = parseFloat(rowData.gram) || 0;
                            const qty = parseFloat(rowData.qty) || 0;
                            const price = parseFloat(rowData.price_per_gram) || 0;
                            const subtotal = gram * qty * price;
                            this.setDataAtRowProp(row, 'subtotal', subtotal);
                        }
                    });

                    updateGrandTotal(this);
                },

                afterRemoveRow() {
                    updateGrandTotal(this);
                },
                afterCreateRow() {
                    updateGrandTotal(this);
                },
            });

            updateGrandTotal(hot);

            // --- event submit ---
            document.getElementById('transactionForm').addEventListener('submit', function(e) {
                const rows = hot.getSourceData().filter(r =>
                    (r.variant_label && r.variant_label.trim()) ||
                    (r.product_name && r.product_name.trim())
                );
                if (rows.length === 0) {
                    e.preventDefault();
                    return alert('Tidak ada item di detail transaksi.');
                }

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'details';
                input.value = JSON.stringify(rows);
                this.appendChild(input);
            });
        });
    </script>


@stop
