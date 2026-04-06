@extends('adminlte::page')

@section('title', 'Proses Pecah Emas')

@section('content_header')
    <h1 class="fw-bold">Tambah Proses Pecah Emas</h1>
@stop

@section('content')
    <div class="shadow-lg card">
        <div class="card-body">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('konversi-emas.simpan') }}">
                @csrf

                {{-- KARAT HIDDEN --}}
                <input type="hidden" name="karat_id" id="karatIdHidden">

                {{-- ======================= INFORMASI UTAMA ======================= --}}
                <h5 class="mb-3 fw-bold">Informasi Utama</h5>

                <div class="mb-4 row g-3">

                    {{-- PILIH STOK --}}
                    <div class="col-md-6">
                        <label class="fw-semibold">Pilih Stok</label>
                        <select name="stock_id" id="stockSelect" class="form-control form-control-lg select2" required>
                            <option value="">-- pilih stok emas --</option>
                            @foreach ($productVariants as $s)
                                <option value="{{ $s->id }}" data-karat-id="{{ $s->karat->id }}"
                                    data-karat-name="{{ $s->karat->name }}" data-weight="{{ $s->weight }}">
                                    {{ $s->product->name }} — {{ $s->karat->name }} — {{ $s->weight }} g —
                                    {{ $s->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KARAT --}}
                    <div class="col-md-6">
                        <label class="fw-semibold">Karat</label>
                        <input type="text" id="karatView" class="form-control form-control-lg" readonly>
                    </div>

                    {{-- BERAT INPUT --}}
                    {{-- <div class="col-md-3">
                        <label class="fw-semibold">Berat Input (g)</label>
                        <input type="number" step="0.001" min="0" name="input_weight" id="inputWeight"
                            class="form-control form-control-lg" required>
                        <small class="text-muted">
                            Berat stok: <span id="stockWeight">0</span> g
                        </small>
                    </div> --}}

                </div>

                <hr class="my-4">

                {{-- ======================= DETAIL BARANG ======================= --}}
                <h5 class="mb-3 fw-bold">Detail Barang</h5>

                <div class="table-responsive">
                    <table class="table align-middle table-bordered" id="detailTable">
                        <thead class="text-center table-light">
                            <tr>
                                <th width="35%">Produk</th>
                                <th width="15%">Gram</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="mb-3 text-end">
                        <button type="button" id="addRow" class="btn btn-success btn-lg">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="mb-4 text-end">
                        <h5 class="fw-bold">
                            Grand Total:
                            <span id="grandTotalGram">0</span>g
                        </h5>
                    </div>
                </div>

                {{-- SUBMIT --}}
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
        const karats = @json($karats);

        $(document).ready(function() {

            $('.select2').select2({
                width: '100%'
            });

            /* ================= PILIH STOK ================= */
            $('#stockSelect').on('change', function() {
                const opt = $(this).find(':selected');

                $('#karatView').val(opt.data('karat-name') || '-');
                $('#karatIdHidden').val(opt.data('karat-id') || '');
                $('#inputWeight').val(opt.data('weight') || 0);
                $('#stockWeight').text(opt.data('weight') || 0);
            });

            /* ================= HITUNG TOTAL GRAM ================= */
            function updateGrandTotal() {
                let total = 0;

                $('#detailTable tbody .weight').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });

                $('#grandTotalGram').text(
                    total.toLocaleString('id-ID', {
                        minimumFractionDigits: 3
                    })
                );
            }

            /* ================= TAMBAH BARIS ================= */
            function addRow() {
                const index = $('#detailTable tbody tr').length;

                const row = `
                <tr>
                    <td>
                        <select name="details[${index}][product_id]"
                            class="form-control form-control-lg select2-product" required>
                            <option value="">-- pilih produk --</option>
                            ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                        </select>
                    </td>

                    <td>
                        <input type="number" step="0.001" min="0"
                            name="details[${index}][weight]"
                            class="form-control form-control-lg weight" required>
                    </td>

                    <td class="text-center">
                        <button type="button"
                            class="btn btn-danger btn-lg removeRow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

                $('#detailTable tbody').append(row);
                $('.select2-product').last().select2({
                    width: '100%'
                });
            }

            /* ================= EVENT ================= */
            $('#addRow').on('click', addRow);

            $(document).on('input', '.weight', updateGrandTotal);

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateGrandTotal();
            });

            /* BARIS PERTAMA */
            addRow();
        });
    </script>

@stop
