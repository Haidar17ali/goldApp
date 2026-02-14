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

            <form id="transactionForm" method="POST" action="{{ route('penjualan.simpan', ['type' => $type]) }}"
                enctype="multipart/form-data">
                @csrf

                {{-- ================= HEADER ================= --}}
                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label class="fw-semibold">Nomor Invoice</label>
                        <input type="text" readonly name="invoice_number" class="form-control form-control-lg"
                            value="{{ $invoiceNumber }}" required>
                    </div>

                    <div class="col-md-4 position-relative" x-data="customerInput({{ Js::from($customers) }})">

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
                            placeholder="Catatan tambahan (opsional)">
                    </div>
                </div>

                {{-- ================= CUSTOMER DETAIL ================= --}}
                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label class="fw-semibold">No. Telp</label>
                        <input type="text" name="customer_phone" id="customerPhone" class="form-control form-control-lg"
                            placeholder="Nomor telepon customer">
                    </div>

                    <div class="col-md-8">
                        <label class="fw-semibold">Alamat</label>
                        <input type="text" name="customer_address" id="customerAddress"
                            class="form-control form-control-lg" placeholder="Alamat customer">
                    </div>
                </div>

                <hr class="my-4">

                {{-- <div x-data="barcodeInput()" class="mb-4 position-relative">

                    <label class="fw-semibold">Scan Barcode / Ketik Barang</label>

                    <input type="text" x-model="query" @input="search" @keydown.enter.prevent="selectFirst"
                        class="form-control form-control-lg" placeholder="Scan barcode atau ketik nama barang" autofocus>

                    <!-- DROPDOWN -->
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
                </div> --}}

                <div x-data="barcodeInput()" class="mb-4 position-relative">

                    <label class="fw-semibold">Scan Barcode / Ketik Barang</label>

                    <div class="input-group input-group-lg">

                        <input type="text" x-model="query" @input="search" @keydown.enter.prevent="selectFirst"
                            class="form-control" placeholder="Scan barcode atau ketik nama barang">

                        <!-- BUTTON CAMERA -->
                        <button type="button" class="btn btn-outline-secondary" @click="openCamera">

                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <!-- AREA CAMERA -->
                    <div x-show="cameraOpen" class="p-2 mt-2 border rounded bg-light">

                        <div id="reader" style="width:100%"></div>

                        <button class="mt-2 btn btn-danger btn-sm" @click="closeCamera">
                            Tutup Kamera
                        </button>
                    </div>



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

                    {{-- <div class="mb-3 text-end">
                    <button type="button" id="addRow" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button> --}}
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
                    <button type="submit" class="px-5 btn btn-primary btn-lg">
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- barcode scanner --}}
    <script src="https://unpkg.com/html5-qrcode"></script>


    <script>
        window.PRODUCT_VARIANTS = @json($productVariants);
    </script>


    <script>
        // function barcodeInput() {
        //     return {
        //         query: '',
        //         results: [],
        //         showDropdown: false,
        //         variants: window.PRODUCT_VARIANTS || [],

        //         search() {
        //             const q = this.query.trim().toLowerCase();
        //             const terms = q.split(/\s+/); // pecah per spasi

        //             // ===== EXACT MATCH (SCAN BARCODE / SKU)
        //             const exact = this.variants.find(v =>
        //                 v.barcode?.toLowerCase() === q ||
        //                 v.sku?.toLowerCase() === q
        //             );

        //             if (exact) {
        //                 this.select(exact);
        //                 return;
        //             }

        //             if (q.length < 2) {
        //                 this.showDropdown = false;
        //                 return;
        //             }

        //             this.results = this.variants.filter(v => {
        //                 // gabungkan semua field yang mau dicari
        //                 const searchableText = `
    //                     ${v.product?.name ?? ''}
    //                     ${v.karat?.name ?? ''}
    //                     ${v.gram ?? ''}
    //                     ${v.sku ?? ''}
    //                     ${v.barcode ?? ''}
    //                 `.toLowerCase();

        //                 // SEMUA kata harus ada
        //                 return terms.every(term => searchableText.includes(term));
        //             }).slice(0, 10);

        //             this.showDropdown = this.results.length > 0;
        //         },


        //         select(item) {
        //             window.addItemToTable(item);
        //             this.reset();
        //         },

        //         selectFirst() {
        //             if (this.results.length === 1) {
        //                 this.select(this.results[0]);
        //             }
        //         },

        //         reset() {
        //             this.query = '';
        //             this.results = [];
        //             this.showDropdown = false;
        //         }
        //     }
        // }
        function barcodeInput() {
            return {
                query: '',
                results: [],
                showDropdown: false,
                variants: window.PRODUCT_VARIANTS || [],

                cameraOpen: false,
                scanner: null,

                search() {
                    const q = this.query.trim().toLowerCase();
                    const terms = q.split(/\s+/);

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
                        const searchableText = `
                    ${v.product?.name ?? ''}
                    ${v.karat?.name ?? ''}
                    ${v.gram ?? ''}
                    ${v.sku ?? ''}
                    ${v.barcode ?? ''}
                `.toLowerCase();

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
                },

                // ================= CAMERA =================

                openCamera() {
                    this.cameraOpen = true;

                    this.$nextTick(() => {
                        this.scanner = new Html5Qrcode("reader");

                        this.scanner.start({
                                facingMode: {
                                    exact: "environment"
                                }
                            }, {
                                fps: 20,
                                qrbox: {
                                    width: 250,
                                    height: 120
                                },
                                aspectRatio: 1.777,
                                formatsToSupport: [
                                    Html5QrcodeSupportedFormats.CODE_128,
                                    Html5QrcodeSupportedFormats.CODE_39,
                                    Html5QrcodeSupportedFormats.EAN_13,
                                    Html5QrcodeSupportedFormats.EAN_8,
                                    Html5QrcodeSupportedFormats.UPC_A,
                                    Html5QrcodeSupportedFormats.UPC_E
                                ]
                            },
                            (decodedText) => {
                                this.onScanSuccess(decodedText);
                            },
                        );

                    });
                },

                closeCamera() {
                    this.cameraOpen = false;

                    if (this.scanner) {
                        this.scanner.stop().then(() => {
                            this.scanner.clear();
                        });
                    }
                },

                onScanSuccess(code) {
                    this.query = code;
                    this.search();

                    const exact = this.variants.find(v =>
                        v.barcode == code || v.sku == code
                    );

                    if (exact) {
                        this.select(exact);
                        this.closeCamera();
                    }
                }
            }
        }
    </script>

    <script>
        window.addItemToTable = function(item) {

            const tableBody = document.querySelector('#detailTable tbody');
            const rowIndex = tableBody.children.length;

            // CEGAH DUPLIKAT
            if ([...tableBody.querySelectorAll('input[name$="[variant_id]"]')]
                .some(i => i.value == item.id)) {
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
                        value="${item.product.name}" readonly>
                </td>

                <td>
                    <input type="text" class="form-control"
                        value="${item.karat?.name ?? '-'}" readonly>
                </td>

                <td>
                    <input type="text" class="form-control"
                        value="${item.gram}" readonly>
                </td>

                <td>
                    <input type="number"
                        class="form-control harga-jual"
                        name="details[${rowIndex}][harga_jual]"
                        value="${item.default_price ?? 0}">
                </td>

                <td class="text-center">
                    <button type="button"
                        class="btn btn-danger btn-lg remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>

                <input type="hidden"
                    name="details[${rowIndex}][variant_id]"
                    value="${item.id}">
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
        }
    </script>

    <script>
        let tableBody, grandTotalEl;

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

        document.addEventListener('DOMContentLoaded', function() {
            const products = @json($products ?? []);
            const karats = @json($karats ?? []);

            tableBody = document.querySelector('#detailTable tbody');
            grandTotalEl = document.getElementById('grandTotalJual');

            // let rowIndex = 0;

            // function createRow() {
            //     const tr = document.createElement('tr');

            //     tr.innerHTML = `
        //         <td>
        //             <select class="form-control form-control-lg select-product"
        //                 name="details[${rowIndex}][product_name]">
        //                 <option value="">-- pilih / ketik produk --</option>
        //                 ${products.map(p => `<option value="${p}">${p}</option>`).join('')}
        //             </select>
        //         </td>

        //         <td>
        //             <select class="form-control form-control-lg select-karat"
        //                 name="details[${rowIndex}][karat_name]">
        //                 <option value="">-- pilih / ketik karat --</option>
        //                 ${karats.map(k => `<option value="${k}">${k}</option>`).join('')}
        //             </select>
        //         </td>

        //         <td>
        //             <input type="number" step="0.001"
        //                 class="form-control form-control-lg gram"
        //                 name="details[${rowIndex}][gram]">
        //         </td>

        //         <td>
        //             <input type="number" step="0.01"
        //                 class="form-control form-control-lg harga-jual"
        //                 name="details[${rowIndex}][harga_jual]">
        //         </td>

        //         <td class="text-center">
        //             <button type="button"
        //                 class="btn btn-danger btn-lg remove-row">
        //                 <i class="fas fa-trash"></i>
        //             </button>
        //         </td>
        //     `;

            //     tableBody.appendChild(tr);

            //     $(tr).find('select').select2({
            //         tags: true,
            //         width: '100%'
            //     });

            //     tr.querySelector('.harga-jual')
            //         .addEventListener('input', updateGrandTotal);

            //     tr.querySelector('.remove-row')
            //         .addEventListener('click', () => {
            //             tr.remove();
            //             updateGrandTotal();
            //         });

            //     rowIndex++;
            // }

            // createRow();
            // document.getElementById('addRow')
            //     .addEventListener('click', createRow);

            // // ===== Customer Select2 =====
            // $('#customerSelect').select2({
            //     tags: true,
            //     width: '100%',
            //     placeholder: '-- pilih / ketik customer --'
            // });

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

    <script>
        function customerInput(customers) {
            return {
                query: '',
                results: [],
                show: false,

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

                    // AUTO ISI
                    document.getElementById('customerPhone').value = item.phone_number ?? ''
                    document.getElementById('customerAddress').value = item.address ?? ''
                }
            }
        }
    </script>

@stop
