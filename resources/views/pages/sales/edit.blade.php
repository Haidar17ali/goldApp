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
                        <input type="text" name="invoice_number" class="form-control form-control-lg"
                            value="{{ old('invoice_number', $transaction->invoice_number) }}" required>
                    </div>

                    <div class="col-md-4 position-relative" x-data="customerInput({
                        customers: {{ Js::from($customers) }},
                        initialName: '{{ old('customer_name', $transaction->customer?->name) }}',
                        initialPhone: '{{ old('customer_phone', $transaction->customer?->phone_number) }}',
                        initialAddress: '{{ old('customer_address', $transaction->customer?->address) }}'
                    })" x-init="init()">

                        <label class="fw-semibold">Customer</label>

                        <input type="text" name="customer_name" x-model="query" @input="search" @focus="search"
                            class="form-control form-control-lg" placeholder="Ketik / pilih customer" required>

                        <!-- DROPDOWN -->
                        <div class="shadow list-group position-absolute w-100" x-show="show" @click.outside="show = false"
                            style="z-index:1050">

                            <template x-for="item in results" :key="item.id">
                                <button type="button" class="list-group-item list-group-item-action" @click="select(item)">
                                    <strong x-text="item.name"></strong><br>
                                    <small class="text-muted" x-text="item.address"></small>
                                </button>
                            </template>

                            <div class="list-group-item text-muted" x-show="results.length === 0">
                                Customer baru — isi manual
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <label class="fw-semibold">Catatan</label>
                        <input type="text" name="note" class="form-control form-control-lg"
                            value="{{ old('note', $transaction->note) }}" placeholder="Catatan tambahan (opsional)">
                    </div>
                </div>

                {{-- ================= CUSTOMER DETAIL ================= --}}
                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label class="fw-semibold">No. Telp</label>
                        <input type="text" name="customer_phone" id="customerPhone" class="form-control form-control-lg"
                            value="{{ old('customer_phone', $transaction->customer?->phone_number) }}"
                            placeholder="Nomor telepon customer">
                    </div>

                    <div class="col-md-8">
                        <label class="fw-semibold">Alamat</label>
                        <input type="text" name="customer_address" id="customerAddress"
                            class="form-control form-control-lg"
                            value="{{ old('customer_address', $transaction->customer?->address) }}"
                            placeholder="Alamat customer">
                    </div>
                </div>

                <hr class="my-4">

                <div x-data="barcodeInput()" class="mb-4 position-relative">
                    <label class="fw-semibold">Scan Barcode / Ketik Barang</label>

                    <input type="text" x-model="query" @input="search" @keydown.enter.prevent="selectFirst"
                        class="form-control form-control-lg" placeholder="Scan barcode atau ketik nama barang">

                    <div class="shadow list-group position-absolute w-100" x-show="showDropdown"
                        @click.outside="showDropdown = false" style="z-index:1050">

                        <template x-for="item in results" :key="item.id">
                            <button type="button" class="list-group-item list-group-item-action" @click="select(item)">
                                <strong x-text="item.barcode ?? item.sku"></strong>
                                —
                                <span x-text="item.product.name"></span>
                                —
                                <span x-text="item.karat.name"></span>
                                —
                                <span x-text="item.gram + 'gr'"></span>

                                <!-- BADGE NEW (HANYA JIKA TYPE = new) -->
                                <span x-show="item.type === 'new'" class="badge bg-success ms-2">
                                    NEW
                                </span>
                            </button>
                        </template>

                        <div class="list-group-item text-muted" x-show="results.length === 0">
                            Tidak ditemukan
                        </div>
                    </div>
                </div>


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
                    <button type="submit" class="px-5 btn btn-primary btn-lg">
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

{{-- ================= JS ================= --}}
@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        /* ======================================================
                                   GLOBAL DATA
                                ====================================================== */
        window.PRODUCT_VARIANTS = @json($productVariants);
        window.EXISTING_DETAILS = @json($details);

        let tableBody, grandTotalEl;

        /* ======================================================
           DOM READY
        ====================================================== */
        document.addEventListener('DOMContentLoaded', function() {

            tableBody = document.querySelector('#detailTable tbody');
            grandTotalEl = document.getElementById('grandTotalJual');

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

            // ===== PRELOAD EDIT DATA =====
            if (window.EXISTING_DETAILS?.length) {
                window.EXISTING_DETAILS.forEach(item => {
                    addItemToTable(item, true);
                });
            }

            updateGrandTotal();
        });

        /* ======================================================
           GRAND TOTAL
        ====================================================== */
        window.updateGrandTotal = function() {
            let total = 0;

            tableBody.querySelectorAll('.harga-jual').forEach(el => {
                total += parseFloat(el.value || 0);
            });

            grandTotalEl.textContent = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });

            document.dispatchEvent(new CustomEvent('grandTotalChanged', {
                detail: {
                    total
                }
            }));
        };

        /* ======================================================
           ADD ITEM TO TABLE (EDIT + CREATE)
        ====================================================== */
        window.addItemToTable = function(item, fromEdit = false) {

            const rowIndex = tableBody.children.length;

            // ===== CEGAH DUPLIKAT =====
            if ([...tableBody.querySelectorAll('input[name$="[variant_id]"]')]
                .some(i => i.value == (item.variant_id ?? item.id))) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang sudah ditambahkan'
                });
                return;
            }

            const tr = document.createElement('tr');

            tr.innerHTML = `
            <td>
                <input type="text" class="form-control"
                    value="${item.product?.name ?? item.product_name}" readonly>
            </td>

            <td>
                <input type="text" class="form-control"
                    value="${item.karat?.name ?? item.karat_name ?? '-'}" readonly>
            </td>

            <td>
                <input type="text" class="form-control"
                    value="${item.gram}" readonly>
            </td>

            <td>
                <input type="number"
                    class="form-control harga-jual"
                    name="details[${rowIndex}][harga_jual]"
                    value="${item.harga_jual ?? item.default_price ?? 0}">
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-lg remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>

            <input type="hidden"
                name="details[${rowIndex}][variant_id]"
                value="${item.variant_id ?? item.id}">
        `;

            tableBody.appendChild(tr);

            tr.querySelector('.remove-row')
                .addEventListener('click', () => {
                    tr.remove();
                    updateGrandTotal();
                });

            tr.querySelector('.harga-jual')
                .addEventListener('input', updateGrandTotal);

            updateGrandTotal();
        };

        /* ======================================================
           ALPINE BARCODE INPUT (SAMA DENGAN CREATE)
        ====================================================== */
        function barcodeInput() {
            return {
                query: '',
                results: [],
                showDropdown: false,
                variants: window.PRODUCT_VARIANTS || [],

                search() {
                    const q = this.query.trim().toLowerCase();
                    const terms = q.split(/\s+/); // pecah per spasi

                    // ===== EXACT MATCH (SCAN BARCODE / SKU)
                    const exact = this.variants.find(v =>
                        v.barcode?.toLowerCase() === q ||
                        v.sku?.toLowerCase() === q
                    );

                    if (exact) {
                        this.select(exact);
                        return;
                    }

                    if (q.length < 2) {
                        this.showDropdown = false;
                        return;
                    }

                    this.results = this.variants.filter(v => {
                        // gabungkan semua field yang mau dicari
                        const searchableText = `
                            ${v.product?.name ?? ''}
                            ${v.karat?.name ?? ''}
                            ${v.gram ?? ''}
                            ${v.sku ?? ''}
                            ${v.barcode ?? ''}
                        `.toLowerCase();

                        // SEMUA kata harus ada
                        return terms.every(term => searchableText.includes(term));
                    }).slice(0, 10);

                    this.showDropdown = this.results.length > 0;
                },

                select(item) {
                    window.addItemToTable(item);
                    this.reset();
                },

                selectFirst() {
                    if (this.results.length === 1) {
                        this.select(this.results[0]);
                    }
                },

                reset() {
                    this.query = '';
                    this.results = [];
                    this.showDropdown = false;
                }
            }
        }
    </script>
    <script>
        function customerInput({
            customers,
            initialName,
            initialPhone,
            initialAddress
        }) {
            return {
                query: '',
                results: [],
                show: false,

                init() {
                    // SET DATA AWAL (MODE EDIT)
                    this.query = initialName ?? ''
                    document.getElementById('customerPhone').value = initialPhone ?? ''
                    document.getElementById('customerAddress').value = initialAddress ?? ''
                },

                search() {
                    if (!this.query) {
                        this.results = []
                        this.show = false
                        return
                    }

                    this.results = customers.filter(c =>
                        c.name.toLowerCase().includes(this.query.toLowerCase())
                    )

                    this.show = true
                },

                select(item) {
                    this.query = item.name
                    this.show = false

                    document.getElementById('customerPhone').value = item.phone_number ?? ''
                    document.getElementById('customerAddress').value = item.address ?? ''
                }
            }
        }
    </script>

@stop
