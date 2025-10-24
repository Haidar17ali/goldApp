@extends('adminlte::page')

@section('title', 'Edit Transaksi')

@section('content_header')
    <h1 class="fw-bold">Edit Transaksi Pembelian</h1>
@stop

@section('content')
    <div class="card shadow-lg">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first('msg') }}
                </div>
            @endif

            <form id="transactionForm" method="POST"
                action="{{ route('transaksi.update', ['type' => $type, 'purchaseType' => $purchaseType, 'id' => $transaction->id]) }}">
                @csrf
                @method('patch')

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="fw-semibold">Nomor Invoice</label>
                        <input type="text" name="invoice_number" class="form-control form-control-lg"
                            value="{{ old('invoice_number', $transaction->invoice_number) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Nama Customer</label>
                        <input type="text" name="customer_name" class="form-control form-control-lg"
                            value="{{ old('customer_name', $transaction->customer_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Catatan</label>
                        <input type="text" name="note" class="form-control form-control-lg"
                            value="{{ old('note', $transaction->note) }}" placeholder="Catatan tambahan (opsional)">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">Detail Barang</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="detailTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 25%">Produk</th>
                                <th style="width: 15%">Karat</th>
                                <th style="width: 10%">Gram</th>
                                <th style="width: 10%">Qty</th>
                                <th style="width: 15%">Harga/gr</th>
                                <th style="width: 15%">Subtotal</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="text-end mb-3">
                    <button type="button" id="addRow" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <div class="text-end mb-4">
                    <h4><strong>Grand Total: Rp <span id="grandTotal">0</span></strong></h4>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-1"></i> Update Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')

    <style>
        /* 🧩 Biar semua input sejajar secara vertikal */
        #detailTable td {
            vertical-align: middle !important;
        }

        /* 🧱 Perbaiki Select2 agar tinggi & posisi teks sejajar input lain */
        .select2-container--default .select2-selection--single {
            height: calc(2.875rem + 2px) !important;
            /* sesuai .form-control-lg */
            padding: 0.5rem 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.5rem !important;
        }

        /* Teks select2 center */
        .select2-selection__rendered {
            line-height: 1.5rem !important;
            font-size: 1rem !important;
        }

        /* Panah dropdown sejajar tengah */
        .select2-selection__arrow {
            height: 100% !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        /* Pastikan input besar sejajar semua */
        .form-control-lg {
            height: calc(2.875rem + 2px);
        }

        /* Table cell padding lebih rapi */
        #detailTable th,
        #detailTable td {
            padding: 0.5rem;
        }
    </style>
    {{-- @include('transaksi._style') bisa reuse dari create --}}
@stop

@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const variants = @json($variants ?? []);
            const products = [...new Set(variants.map(v => v.product_name))];
            const karats = [...new Set(variants.map(v => v.karat_name))];

            const tableBody = document.querySelector('#detailTable tbody');
            const grandTotalEl = document.getElementById('grandTotal');
            const existingDetails = @json($details);

            function formatNumber(num) {
                return num.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
            }

            function updateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.subtotal').forEach(el => {
                    total += parseFloat(el.value) || 0;
                });
                grandTotalEl.textContent = formatNumber(total);
            }

            function createRow(data = {}) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
        <td>
            <select class="form-control form-control-lg select-product" name="details[][product_name]">
                <option value="">-- pilih / ketik produk --</option>
                ${products.map(p => `<option value="${p}">${p}</option>`).join('')}
            </select>
        </td>
        <td>
            <select class="form-control form-control-lg select-karat" name="details[][karat_name]">
                <option value="">-- pilih / ketik karat --</option>
                ${karats.map(k => `<option value="${k}">${k}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" step="0.001" class="form-control form-control-lg gram" name="details[][gram]" value="${data.gram || ''}"></td>
        <td><input type="number" step="1" class="form-control form-control-lg qty" name="details[][qty]" value="${data.qty || ''}"></td>
        <td><input type="number" step="0.01" class="form-control form-control-lg price" name="details[][price_per_gram]" value="${data.price_per_gram || ''}"></td>
        <td><input type="text" readonly class="form-control form-control-lg subtotal" name="details[][subtotal]" value="${data.subtotal || ''}"></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-lg remove-row"><i class="fas fa-trash"></i></button>
        </td>
    `;
                tableBody.appendChild(tr);

                // Inisialisasi Select2
                const $productSelect = $(tr).find('.select-product');
                const $karatSelect = $(tr).find('.select-karat');

                $productSelect.select2({
                    tags: true,
                    width: '100%'
                });
                $karatSelect.select2({
                    tags: true,
                    width: '100%'
                });

                // 🟢 FIX: set nilai setelah Select2 aktif
                if (data.product_name) {
                    // tambahkan opsi jika belum ada (kalau user dulu buat custom)
                    if (!$productSelect.find(`option[value="${data.product_name}"]`).length) {
                        const newOption = new Option(data.product_name, data.product_name, true, true);
                        $productSelect.append(newOption).trigger('change');
                    } else {
                        $productSelect.val(data.product_name).trigger('change');
                    }
                }

                if (data.karat_name) {
                    if (!$karatSelect.find(`option[value="${data.karat_name}"]`).length) {
                        const newOption = new Option(data.karat_name, data.karat_name, true, true);
                        $karatSelect.append(newOption).trigger('change');
                    } else {
                        $karatSelect.val(data.karat_name).trigger('change');
                    }
                }

                // Hitung subtotal
                tr.querySelectorAll('.gram, .qty, .price').forEach(el => {
                    el.addEventListener('input', () => {
                        const gram = parseFloat(tr.querySelector('.gram').value) || 0;
                        const qty = parseFloat(tr.querySelector('.qty').value) || 0;
                        const price = parseFloat(tr.querySelector('.price').value) || 0;
                        const subtotal = gram * qty * price;
                        tr.querySelector('.subtotal').value = subtotal.toFixed(2);
                        updateGrandTotal();
                    });
                });

                // Tombol hapus
                tr.querySelector('.remove-row').addEventListener('click', () => {
                    tr.remove();
                    updateGrandTotal();
                });
            }


            // isi data lama
            if (existingDetails.length) {
                existingDetails.forEach(d => createRow(d));
            } else {
                createRow();
            }

            updateGrandTotal();

            document.getElementById('addRow').addEventListener('click', () => createRow());

            document.getElementById('transactionForm').addEventListener('submit', function(e) {
                const rows = [];
                tableBody.querySelectorAll('tr').forEach(tr => {
                    const product = tr.querySelector('.select-product').value;
                    const karat = tr.querySelector('.select-karat').value;
                    const gram = tr.querySelector('.gram').value;
                    const qty = tr.querySelector('.qty').value;
                    const price = tr.querySelector('.price').value;
                    const subtotal = tr.querySelector('.subtotal').value;

                    if (product && karat && gram && qty && price) {
                        rows.push({
                            product_name: product,
                            karat_name: karat,
                            gram,
                            qty,
                            price_per_gram: price,
                            subtotal
                        });
                    }
                });

                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal satu detail barang.');
                    return;
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
