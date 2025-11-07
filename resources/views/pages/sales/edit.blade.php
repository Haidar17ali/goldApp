@extends('adminlte::page')

@section('title', 'Edit Transaksi')

@section('content_header')
    <h1 class="fw-bold">Edit Transaksi Penjualan</h1>
@stop

@section('content')
    <div class="shadow-lg card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first('msg') }}
                </div>
            @endif

            <form id="transactionForm" method="POST"
                action="{{ route('penjualan.update', ['type' => $type, 'id' => $transaction->id]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="mb-4 row">
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

                <h5 class="mb-3 fw-bold">Detail Barang</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-bordered" id="detailTable">
                        <thead class="text-center table-light">
                            <tr>
                                <th style="width: 25%">Produk</th>
                                <th style="width: 15%">Karat</th>
                                <th style="width: 15%">Gram</th>
                                <th style="width: 15%">Harga Jual</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="float-right mb-4 text-end">
                        <h5><strong>Grand Total Jual: Rp <span id="grandTotalJual">0</span></strong></h5>
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <button type="button" id="addRow" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                @include('components.payment-gateway', [
                    'bankAccounts' => $bankAccounts,
                    'payment_method' => $transaction->payment_method,
                    'bank_account_id' => $transaction->bank_account_id,
                    'transfer_amount' => $transaction->transfer_amount,
                    'cash_amount' => $transaction->cash_amount,
                    'reference_no' => $transaction->reference_no,
                ])

                @include('components.camera', [
                    'transaction' => $transaction,
                ])

                <div class="text-end">
                    <button type="submit" class="px-5 btn btn-primary btn-lg">
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
            const tableBody = document.querySelector('#detailTable tbody');
            const grandTotalEl = document.getElementById('grandTotalJual');

            let rowIndex = 0;

            function updateGrandTotal() {
                let total = 0;
                tableBody.querySelectorAll('tr').forEach(tr => {
                    const harga = parseFloat(tr.querySelector('.harga-jual')?.value || 0);
                    total += harga;
                });

                grandTotalEl.textContent = total.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });

                document.dispatchEvent(new CustomEvent('grandTotalChanged', {
                    detail: {
                        total: total
                    }
                }));
            }

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
                            class="form-control form-control-lg harga-jual"
                            name="details[${index}][harga_jual]" value="${data.harga_jual ?? ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-lg remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;

                tableBody.appendChild(tr);

                $(tr).find('.select-product').select2({
                    tags: true,
                    width: '100%'
                });
                $(tr).find('.select-karat').select2({
                    tags: true,
                    width: '100%'
                });

                if (data.product_name) $(tr).find('.select-product').val(data.product_name).trigger('change');
                if (data.karat_name) $(tr).find('.select-karat').val(data.karat_name).trigger('change');

                tr.querySelectorAll('.gram, .harga-jual').forEach(el => {
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
