@extends('adminlte::page')

@section('title', 'Tambah Transaksi')

@section('content_header')
    <h1 class="fw-bold">Tambah Transaksi Penjualan</h1>
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
                action="{{ route('penjualan.simpan', ['type' => $type]) }}"
                enctype="multipart/form-data">
                @csrf

                {{-- ================= HEADER ================= --}}
                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label class="fw-semibold">Nomor Invoice</label>
                        <input type="text" name="invoice_number"
                            class="form-control form-control-lg"
                            value="{{ $invoiceNumber }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Customer</label>
                        <select name="customer_name" id="customerSelect"
                            class="form-control form-control-lg" required>
                            <option value="">-- pilih / ketik customer --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->name }}"
                                    data-phone="{{ $c->phone_number }}"
                                    data-address="{{ $c->address }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Catatan</label>
                        <input type="text" name="note"
                            class="form-control form-control-lg"
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
                            placeholder="Nomor telepon customer">
                    </div>

                    <div class="col-md-8">
                        <label class="fw-semibold">Alamat</label>
                        <input type="text" name="customer_address"
                            id="customerAddress"
                            class="form-control form-control-lg"
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
                                <th style="width: 25%">Produk</th>
                                <th style="width: 15%">Karat</th>
                                <th style="width: 15%">Gram</th>
                                <th style="width: 15%">Harga Jual</th>
                                <th style="width: 5%"></th>
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
                    'payment_method' => $transaction->payment_method ?? null,
                    'bank_account_id' => $transaction->bank_account_id ?? null,
                    'transfer_amount' => $transaction->transfer_amount ?? null,
                    'cash_amount' => $transaction->cash_amount ?? null,
                    'reference_no' => $transaction->reference_no ?? null,
                ])

                @include('components.camera')

                <div class="text-end">
                    <button type="submit"
                        class="px-5 btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Simpan Transaksi
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

@section('plugins.Toast', true)

@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const products = @json($products ?? []);
        const karats = @json($karats ?? []);
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

        function createRow() {
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
                        name="details[${rowIndex}][gram]">
                </td>
                <td>
                    <input type="number" step="0.01"
                        class="form-control form-control-lg harga-jual"
                        name="details[${rowIndex}][harga_jual]">
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

            tr.querySelectorAll('.harga-jual')
                .forEach(el => el.addEventListener('input', updateGrandTotal));

            tr.querySelector('.remove-row')
                .addEventListener('click', () => {
                    tr.remove();
                    updateGrandTotal();
                });

            rowIndex++;
        }

        createRow();
        document.getElementById('addRow').addEventListener('click', createRow);

        // ===== Customer Select2 =====
        $('#customerSelect').select2({
            tags: true,
            width: '100%',
            placeholder: '-- pilih / ketik customer --'
        });

        $('#customerSelect').on('change', function() {
            const selected = $(this).find(':selected');
            $('#customerPhone').val(selected.data('phone') || '');
            $('#customerAddress').val(selected.data('address') || '');
        });
    });
</script>

<script>
    $('#transactionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: this.action,
            method: this.method,
            data: formData,
            processData: false,
            contentType: false,
            success(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    window.open(res.redirect_print, '_blank');
                    window.location.href = res.redirect_index;
                });
            },
            error(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    });
</script>
@stop
