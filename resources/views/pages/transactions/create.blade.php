@extends('adminlte::page')

@section('title', 'Tambah Transaksi')

@section('content_header')
    <h1 class="fw-bold">Tambah Transaksi Pembelian</h1>
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
                action="{{ route('transaksi.simpan', ['type' => $type, 'purchaseType' => $purchaseType]) }}"
                enctype="multipart/form-data">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="fw-semibold">Nomor Invoice</label>
                        <input type="text" name="invoice_number" class="form-control form-control-lg"
                            value="{{ $invoiceNumber }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Nama Customer</label>
                        <input type="text" name="customer_name" class="form-control form-control-lg"
                            placeholder="Masukkan nama customer" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Catatan</label>
                        <input type="text" name="note" class="form-control form-control-lg"
                            placeholder="Catatan tambahan (opsional)">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">Detail Barang</h5>
                <div class="table-responsive">
                    <!-- Header table: hapus Qty & Subtotal, tambahkan Harga Jual & Harga Beli -->
                    <table class="table table-bordered align-middle" id="detailTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 25%">Produk</th>
                                <th style="width: 15%">Karat</th>
                                <th style="width: 15%">Gram</th>
                                @if ($type == 'penjualan')
                                    <th style="width: 15%">Harga Jual</th>
                                @else
                                    <th style="width: 15%">Harga Beli</th>
                                @endif
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <!-- Grand totals: dua nilai terpisah -->
                    <div class="text-end mb-4 float-right">
                        @if ($type == 'penjualan')
                            <h5><strong>Grand Total Jual: Rp <span id="grandTotalJual">0</span></strong></h5>
                        @else
                            <h5><strong>Grand Total Beli: Rp <span id="grandTotalBeli">0</span></strong></h5>
                        @endif
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="button" id="addRow" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                @include('components.payment-gateway', [
                    'bankAccounts' => $bankAccounts,
                    'payment_method' => $transaction->payment_method ?? null,
                    'bank_account_id' => $transaction->bank_account_id ?? null,
                    'transfer_amount' => $transaction->transfer_amount ?? null,
                    'cash_amount' => $transaction->cash_amount ?? null,
                    'reference_no' => $transaction->reference_no ?? null,
                ])

                @if ($type == 'penjualan')
                    @include('components.camera')
                @endif

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-1"></i> Simpan Transaksi
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
@stop

@section('js')
    @stack('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const products = @json($products ?? []);
            const karats = @json($karats ?? []);
            const existingDetails = @json($details ?? []);
            const type = "{{ $type }}";
            const tableBody = document.querySelector('#detailTable tbody');
            const grandTotalEl = document.getElementById('grandTotalBeli');
            const cashInput = document.querySelector('input[name="cash_amount"]');
            const transferInput = document.querySelector('input[name="transfer_amount"]');

            let rowIndex = 0;

            // 🔹 Hitung ulang total dan update nominal pembayaran
            function updateGrandTotal() {
                let total = 0;
                tableBody.querySelectorAll('tr').forEach(tr => {
                    const harga = parseFloat(tr.querySelector('.harga-beli')?.value || 0);
                    total += harga;
                });

                grandTotalEl.textContent = total.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });

                // Auto set nominal tunai/transfer sesuai total
                if (cashInput) cashInput.value = total.toFixed(2);
                if (transferInput) transferInput.value = total.toFixed(2);
            }

            // 🔹 Tambah baris tabel
            function createRow(data = {}) {
                const index = rowIndex++;
                const tr = document.createElement('tr');

                tr.innerHTML = `
            <td>
                <select class="form-control form-control-lg select-product"
                    name="details[${index}][product_name]">
                    <option value="">-- pilih / ketik produk --</option>
                    ${products.map(p => `<option value="${p}">${p}</option>`).join('')}
                </select>
            </td>
            <td>
                <select class="form-control form-control-lg select-karat"
                    name="details[${index}][karat_name]">
                    <option value="">-- pilih / ketik karat --</option>
                    ${karats.map(k => `<option value="${k}">${k}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" step="0.001" min="0"
                    class="form-control form-control-lg gram"
                    name="details[${index}][gram]" value="${data.gram ?? ''}">
            </td>
            <td>
                <input type="number" step="0.01" min="0"
                    class="form-control form-control-lg harga-beli"
                    name="details[${index}][harga_beli]" value="${data.harga_beli ?? ''}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-lg remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

                tableBody.appendChild(tr);

                // Init select2
                $(tr).find('.select-product').select2({
                    tags: true,
                    width: '100%'
                });
                $(tr).find('.select-karat').select2({
                    tags: true,
                    width: '100%'
                });

                // Set nilai default
                if (data.product_name) $(tr).find('.select-product').val(data.product_name).trigger('change');
                if (data.karat_name) $(tr).find('.select-karat').val(data.karat_name).trigger('change');

                // Event listener update total
                tr.querySelectorAll('.gram, .harga-beli').forEach(el => {
                    el.addEventListener('input', updateGrandTotal);
                });

                tr.querySelector('.remove-row').addEventListener('click', () => {
                    tr.remove();
                    updateGrandTotal();
                });
            }

            // 🔹 Load existing detail dari DB
            if (existingDetails.length) {
                existingDetails.forEach(d => createRow(d));
            } else {
                createRow();
            }

            // 🔹 Tombol tambah baris
            document.getElementById('addRow').addEventListener('click', () => createRow());

            // 🔹 Hitung total awal
            updateGrandTotal();
        });
    </script>

@stop
