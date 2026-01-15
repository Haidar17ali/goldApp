@extends('adminlte::page')

@section('title', 'Proses Pecah Emas')

@section('content_header')
    <h1 class="fw-bold">Tambah Proses Pecah Emas</h1>
@stop

@section('content')
    <div class="shadow-lg card">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('konversi-emas.simpan') }}">
                @csrf

                {{-- KARAT YANG DIPAKAI DETAIL --}}
                <input type="hidden" name="karat_id" id="karatIdHidden">

                {{-- ======================= HEADER FORM ======================= --}}
                <h5 class="mb-3 fw-bold">Informasi Utama</h5>
                <div class="mb-4 row g-3">

                    {{-- PILIH STOCK --}}
                    <div class="col-md-6">
                        <label class="fw-semibold">Pilih Stok</label>
                        <select name="stock_id" id="stockSelect" class="form-control form-control-lg select2" required>
                            <option value="">-- pilih stok emas --</option>
                            @foreach ($stocks as $s)
                                <option value="{{ $s->id }}" data-karat-name="{{ $s->karat->name }}"
                                    data-karat-id="{{ $s->karat->id }}" data-weight="{{ $s->weight }}">
                                    {{ $s->product->name }} — {{ $s->karat->name }} — {{ $s->weight }} g —
                                    {{ $s->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KARAT OTOMATIS --}}
                    <div class="col-md-3">
                        <label class="fw-semibold">Karat</label>
                        <input type="text" id="karatView" class="form-control form-control-lg" readonly>
                    </div>

                    {{-- BERAT INPUT --}}
                    <div class="col-md-3">
                        <label class="fw-semibold">Berat Input (g)</label>
                        <input type="number" step="0.001" min="0" name="input_weight" id="inputWeight"
                            class="form-control form-control-lg" required>
                        <small class="text-muted">Berat stok: <span id="stockWeight">0</span> g</small>
                    </div>

                </div>

                <hr class="my-4">

                <hr class="my-4">

                <div x-data="barcodeInput()" class="mb-4 position-relative">

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
                                <span x-text="item.gram + ' gr'"></span>
                            </button>
                        </template>

                        <div class="list-group-item text-muted" x-show="results.length === 0">
                            Tidak ditemukan
                        </div>
                    </div>
                </div>


                {{-- ======================= DETAIL OUTPUT ======================= --}}
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
                            <strong>
                                Grand Total Jual:
                                Rp <span id="grandTotalJual">0</span>
                            </strong>
                        </h5>
                    </div>
                </div>


                <div class="text-end">
                    <button type="submit" class="px-5 btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Simpan Proses
                    </button>
                </div>

            </form>

        </div>
    </div>
@stop



@section('css')
    <style>
        .select2-container .select2-selection--single {
            height: 2.875rem !important;
            padding: 0.55rem 0.75rem;
            border-radius: 0.5rem;
        }
    </style>
@stop

@section('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const products = @json($products);

        $(document).ready(function() {

            $('.select2').select2();

            // ==========================================
            // PILIH STOCK → set karat & berat otomatis
            // ==========================================
            $('#stockSelect').on('change', function() {
                let opt = $(this).find(':selected');

                let karatName = opt.data('karat-name') || '-';
                let karatId = opt.data('karat-id') || null;
                let weight = opt.data('weight') || 0;

                $('#karatView').val(karatName);
                $('#karatIdHidden').val(karatId);

                $('#inputWeight').val(weight);
                $('#stockWeight').text(weight);
            });

            // ==========================================
            // FUNGSI TAMBAH BARIS DETAIL
            // ==========================================
            function addRow() {
                let index = $('#detailTable tbody tr').length;

                let row = `
                <tr>
                    <td>
                        <select name="details[${index}][product_id]"
                            class="form-control form-control-lg select2-product">
                            <option value="">-- pilih produk --</option>
                            ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                        </select>
                    </td>

                    <td>
                        <input type="number" step="0.001" min="0"
                            name="details[${index}][weight]"
                            class="form-control form-control-lg" required>
                    </td>

                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-lg removeRow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;

                $('#detailTable tbody').append(row);

                $(".select2-product").last().select2({
                    width: "100%"
                });
            }

            // ==========================================
            // HAPUS BARIS
            // ==========================================
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });

            $('#addRow').on('click', addRow);

            // BARIS PERTAMA DEFAULT
            addRow();
        });
    </script>

    {{-- dropdown --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('barcodeInput', () => ({
                query: '',
                results: [],
                showDropdown: false,

                search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.showDropdown = false;
                        return;
                    }

                    fetch(`/api/products/search?q=${this.query}`)
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.showDropdown = true;
                        });
                },

                select(item) {
                    addItemToTable(item);
                    this.reset();
                },

                selectFirst() {
                    if (this.results.length > 0) {
                        this.select(this.results[0]);
                    }
                },

                reset() {
                    this.query = '';
                    this.results = [];
                    this.showDropdown = false;
                }
            }))
        });
    </script>


@stop
