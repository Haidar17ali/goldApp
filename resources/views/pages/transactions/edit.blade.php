@extends('adminlte::page')

@section('title', 'Edit Transaksi')

@section('content_header')
    <h1 class="fw-bold">Edit Transaksi {{ ucfirst($type) }}</h1>
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
                action="{{ route('transaksi.update', ['type' => $type, 'purchaseType' => $purchaseType, 'id' => $transaction->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')

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

                {{-- 🔹 Payment Gateway --}}
                @include('components.payment-gateway', [
                    'bankAccounts' => $bankAccounts,
                    'payment_method' => $transaction->payment_method ?? null,
                    'bank_account_id' => $transaction->bank_account_id ?? null,
                    'transfer_amount' => $transaction->transfer_amount ?? null,
                    'cash_amount' => $transaction->cash_amount ?? null,
                    'reference_no' => $transaction->reference_no ?? null,
                ])

                {{-- 🔹 Camera (untuk penjualan) --}}
                @if ($type == 'penjualan')
                    @include('components.camera', [
                        'existingPhotos' => $transaction->photos ?? [],
                    ])
                @endif

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
        #detailTable td {
            vertical-align: middle !important;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.875rem + 2px) !important;
            padding: 0.5rem 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.5rem !important;
        }

        .select2-selection__rendered {
            line-height: 1.5rem !important;
            font-size: 1rem !important;
        }

        .select2-selection__arrow {
            height: 100% !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .form-control-lg {
            height: calc(2.875rem + 2px);
        }

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

            let grandTotalEl = type === 'penjualan' ?
                document.getElementById('grandTotalJual') :
                document.getElementById('grandTotalBeli');

            let rowIndex = 0;

            function updateGrandTotal() {
                let total = 0;
                tableBody.querySelectorAll('tr').forEach(tr => {
                    const harga = parseFloat(
                        type === 'penjualan' ?
                        (tr.querySelector('.harga-jual')?.value || 0) :
                        (tr.querySelector('.harga-beli')?.value || 0)
                    ) || 0;
                    total += harga;
                });

                if (grandTotalEl) {
                    grandTotalEl.textContent = total.toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                }

                document.dispatchEvent(new CustomEvent('grandTotalChanged', {
                    detail: {
                        total
                    }
                }));
            }

            function createRow(data = {}) {
                const currentIndex = rowIndex++;
                const tr = document.createElement('tr');

                // Pastikan produk & karat dari existing data ikut dimasukkan ke daftar
                if (data.product_name && !products.includes(data.product_name)) {
                    products.push(data.product_name);
                }
                if (data.karat_name && !karats.includes(data.karat_name)) {
                    karats.push(data.karat_name);
                }

                let hargaColumn = '';
                if (type === 'penjualan') {
                    hargaColumn = `
                        <td>
                            <input type="number" step="0.01" min="0"
                                class="form-control form-control-lg harga-jual"
                                name="details[${currentIndex}][harga_jual]"
                                value="${data.harga_jual ?? ''}">
                        </td>`;
                } else {
                    hargaColumn = `
                        <td>
                            <input type="number" step="0.01" min="0"
                                class="form-control form-control-lg harga-beli"
                                name="details[${currentIndex}][harga_beli]"
                                value="${data.harga_beli ?? ''}">
                        </td>`;
                }

                tr.innerHTML = `
                    <td>
                        <select class="form-control form-control-lg select-product"
                            name="details[${currentIndex}][product_name]">
                            <option value="">-- pilih / ketik produk --</option>
                            ${products.map(p => `<option value="${p}">${p}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <select class="form-control form-control-lg select-karat"
                            name="details[${currentIndex}][karat_name]">
                            <option value="">-- pilih / ketik karat --</option>
                            ${karats.map(k => `<option value="${k}">${k}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.001" min="0"
                            class="form-control form-control-lg gram"
                            name="details[${currentIndex}][gram]"
                            value="${data.gram ?? ''}">
                    </td>
                    ${hargaColumn}
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-lg remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                tableBody.appendChild(tr);

                // Init select2
                const productSelect = $(tr).find('.select-product');
                const karatSelect = $(tr).find('.select-karat');

                productSelect.select2({
                    tags: true,
                    width: '100%'
                });
                karatSelect.select2({
                    tags: true,
                    width: '100%'
                });

                // Set value sesuai data existing
                if (data.product_name) productSelect.val(data.product_name).trigger('change');
                if (data.karat_name) karatSelect.val(data.karat_name).trigger('change');

                tr.querySelectorAll('.gram, .harga-jual, .harga-beli').forEach(el => {
                    el.addEventListener('input', updateGrandTotal);
                });

                tr.querySelector('.remove-row').addEventListener('click', () => {
                    tr.remove();
                    updateGrandTotal();
                });
            }


            if (existingDetails.length) {
                existingDetails.forEach(d => createRow(d));
            } else {
                createRow();
            }

            document.getElementById('addRow').addEventListener('click', () => createRow());
            updateGrandTotal();
        });
    </script>
@stop
