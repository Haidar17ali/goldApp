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

            {{-- ================= HEADER ================= --}}
            <div class="mb-4 row">
                <div class="col-md-4">
                    <label class="fw-semibold">Nomor Invoice</label>
                    <input type="text" name="invoice_number"
                        class="form-control form-control-lg"
                        value="{{ old('invoice_number', $transaction->invoice_number) }}"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="fw-semibold">Customer</label>
                    <select name="customer_name" id="customerSelect"
                        class="form-control form-control-lg" required>
                        <option value="">-- pilih / ketik customer --</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->name }}"
                                data-phone="{{ $c->phone_number }}"
                                data-address="{{ $c->address }}"
                                {{ $c->id == $transaction->customer_id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-semibold">Catatan</label>
                    <input type="text" name="note"
                        class="form-control form-control-lg"
                        value="{{ old('note', $transaction->note) }}"
                        placeholder="Catatan tambahan (opsional)">
                </div>
            </div>

            {{-- ================= CUSTOMER DETAIL ================= --}}
            <div class="mb-4 row">
                <div class="col-md-4">
                    <label class="fw-semibold">No. Telp</label>
                    <input type="text" name="customer_phone"
                        id="customerPhone"
                        class="form-control form-control-lg"
                        value="{{ old('customer_phone', $transaction->customer?->phone_number) }}"
                        placeholder="Nomor telepon customer">
                </div>

                <div class="col-md-8">
                    <label class="fw-semibold">Alamat</label>
                    <input type="text" name="customer_address"
                        id="customerAddress"
                        class="form-control form-control-lg"
                        value="{{ old('customer_address', $transaction->customer?->address) }}"
                        placeholder="Alamat customer">
                </div>
            </div>

            <hr class="my-4">

            {{-- ================= DETAIL BARANG ================= --}}
            <h5 class="mb-3 fw-bold">Detail Barang</h5>

            <div class="table-responsive">
                <table class="table align-middle table-bordered" id="detailTable">
                    <thead class="text-center table-light">
                        <tr>
                            <th style="width:25%">Produk</th>
                            <th style="width:15%">Karat</th>
                            <th style="width:15%">Gram</th>
                            <th style="width:15%">Harga Jual</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="mb-4 text-end">
                    <h5>
                        <strong>Grand Total Jual:
                            Rp <span id="grandTotalJual">0</span>
                        </strong>
                    </h5>
                </div>
            </div>

            <div class="mb-3 text-end">
                <button type="button" id="addRow"
                    class="btn btn-success btn-lg">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            {{-- ================= PAYMENT ================= --}}
            @include('components.payment-gateway', [
                'bankAccounts' => $bankAccounts,
                'payment_method' => $transaction->payment_method,
                'bank_account_id' => $transaction->bank_account_id,
                'transfer_amount' => $transaction->transfer_amount,
                'cash_amount' => $transaction->cash_amount,
                'reference_no' => $transaction->reference_no,
            ])

            @include('components.camera', ['transaction' => $transaction])

            <div class="text-end">
                <button type="submit"
                    class="px-5 btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Update Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@stop

{{-- ================= CSS ================= --}}
@section('css')
<style>
    #detailTable td { vertical-align: middle !important; }

    .select2-container--default .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
        padding: 0.5rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 0.5rem !important;
    }

    .select2-selection__rendered {
        font-size: 1rem !important;
    }

    .form-control-lg {
        height: calc(2.875rem + 2px);
    }
</style>
@stop

{{-- ================= JS ================= --}}
@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($products ?? []);
    const karats = @json($karats ?? []);
    const existingDetails = @json($details ?? []);
    const tableBody = document.querySelector('#detailTable tbody');
    const grandTotalEl = document.getElementById('grandTotalJual');
    let rowIndex = 0;

    function updateGrandTotal() {
        let total = 0;
        tableBody.querySelectorAll('.harga-jual').forEach(el => {
            total += parseFloat(el.value || 0);
        });

        grandTotalEl.textContent = total.toLocaleString('id-ID', {
            minimumFractionDigits: 2
        });

        document.dispatchEvent(new CustomEvent('grandTotalChanged', {
            detail: { total }
        }));
    }

    function createRow(data = {}) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select class="form-control form-control-lg select-product"
                    name="details[${rowIndex}][product_name]">
                    <option value="">-- pilih / ketik produk --</option>
                    ${products.map(p => `<option value="${p}">${p}</option>`).join('')}
                </select>
            </td>
            <td>
                <select class="form-control form-control-lg select-karat"
                    name="details[${rowIndex}][karat_name]">
                    <option value="">-- pilih / ketik karat --</option>
                    ${karats.map(k => `<option value="${k}">${k}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" step="0.001"
                    class="form-control form-control-lg gram"
                    name="details[${rowIndex}][gram]"
                    value="${data.gram ?? ''}">
            </td>
            <td>
                <input type="number" step="0.01"
                    class="form-control form-control-lg harga-jual"
                    name="details[${rowIndex}][harga_jual]"
                    value="${data.harga_jual ?? ''}">
            </td>
            <td class="text-center">
                <button type="button"
                    class="btn btn-danger btn-lg remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(tr);

        $(tr).find('select').select2({ tags: true, width: '100%' });

        if (data.product_name)
            $(tr).find('.select-product').val(data.product_name).trigger('change');
        if (data.karat_name)
            $(tr).find('.select-karat').val(data.karat_name).trigger('change');

        tr.querySelector('.harga-jual')
            .addEventListener('input', updateGrandTotal);

        tr.querySelector('.remove-row')
            .addEventListener('click', () => {
                tr.remove();
                updateGrandTotal();
            });

        rowIndex++;
    }

    if (existingDetails.length) {
        existingDetails.forEach(d => createRow(d));
    } else {
        createRow();
    }

    $('#customerSelect').select2({
        tags: true,
        width: '100%',
        placeholder: '-- pilih / ketik customer --'
    });

    $('#customerSelect').on('change', function () {
        const selected = $(this).find(':selected');
        $('#customerPhone').val(selected.data('phone') || '');
        $('#customerAddress').val(selected.data('address') || '');
    });

    updateGrandTotal();
});
</script>
@stop
