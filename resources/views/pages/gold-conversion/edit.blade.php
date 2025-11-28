@extends('adminlte::page')

@section('title', 'Edit Proses Pecah Emas')

@section('content_header')
    <h1 class="fw-bold">Edit Proses Pecah Emas</h1>
@stop

@section('content')

    <div class="card shadow-lg">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi kesalahan!</strong><br>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('konversi-emas.update', $conversion->id) }}">
                @csrf
                @method('patch')

                {{-- ======================= HEADER ======================= --}}
                <h5 class="fw-bold mb-3">Informasi Utama</h5>

                <div class="row g-3 mb-4">

                    {{-- PILIH STOK --}}
                    <div class="col-md-6">
                        <label class="fw-semibold">Pilih Stok</label>
                        <select name="stock_id" id="stockSelect" class="form-control form-control-lg select2" required>
                            @foreach ($stocks as $s)
                                <option value="{{ $s->id }}" data-karat-id="{{ $s->karat_id }}"
                                    data-karat="{{ $s->karat->name }}" data-weight="{{ $s->weight }}"
                                    {{ $conversion->stock_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->product->name }} — {{ $s->karat->name }} — {{ $s->weight }} g —
                                    {{ $s->type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KARAT (READONLY VIEW) --}}
                    <div class="col-md-3">
                        <label class="fw-semibold">Karat</label>
                        <input type="text" id="karatView" class="form-control form-control-lg"
                            value="{{ $conversion->kadar->name }}" readonly>
                    </div>

                    {{-- HIDDEN KARAT_ID (DIKIRIM KE UPDATE) --}}
                    <input type="hidden" name="karat_id" id="karatIdInput" value="{{ $conversion->karat_id }}">

                    {{-- INPUT WEIGHT --}}
                    <div class="col-md-3">
                        <label class="fw-semibold">Berat Input (g)</label>
                        <input type="number" step="0.001" min="0" name="input_weight"
                            value="{{ $conversion->input_weight }}" id="inputWeight" class="form-control form-control-lg"
                            required>

                        <small class="text-muted">Berat stok:
                            <span id="stockWeight">{{ $conversion->stock->weight }}</span> g
                        </small>
                    </div>

                </div>

                <hr class="my-4">

                {{-- ======================= DETAIL ======================= --}}
                <h5 class="mb-3 fw-bold">Detail Output</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="detailTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 40%">Produk</th>
                                <th style="width: 20%">Gram</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($conversion->outputs as $i => $d)
                                <tr>
                                    {{-- PRODUK --}}
                                    <td>
                                        <select name="details[{{ $i }}][product_id]"
                                            class="form-control form-control-lg select2-product" required>
                                            @foreach ($products as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ $p->id == $d->product_id ? 'selected' : '' }}>
                                                    {{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- BERAT --}}
                                    <td>
                                        <input type="number" step="0.001" min="0"
                                            name="details[{{ $i }}][weight]"
                                            class="form-control form-control-lg" value="{{ $d->weight }}" required>
                                    </td>

                                    {{-- DELETE --}}
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-lg removeRow">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mb-3 text-end">
                    <button type="button" id="addRow" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-1"></i> Update Proses
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

        $(function() {
            $('.select2').select2();
            $('.select2-product').select2({
                width: "100%"
            });

            let detailIndex = {{ count($conversion->outputs) }};

            // ==============================
            // Ganti Stock → update karat & input weight
            // ==============================
            $('#stockSelect').on('change', function() {
                let opt = $(this).find(':selected');

                let karatName = opt.data('karat');
                let karatId = opt.data('karat-id');
                let weight = opt.data('weight');

                $('#karatView').val(karatName);
                $('#karatIdInput').val(karatId);
                $('#inputWeight').val(weight);
                $('#stockWeight').text(weight);
            });

            // ==============================
            // Tambah Baris Detail
            // ==============================
            $('#addRow').on('click', function() {

                let row = `
                <tr>
                    <td>
                        <select name="details[${detailIndex}][product_id]"
                                class="form-control form-control-lg select2-product" required>
                            <option value="">-- pilih --</option>
                            ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                        </select>
                    </td>

                    <td>
                        <input type="number" step="0.001" min="0"
                            name="details[${detailIndex}][weight]"
                            class="form-control form-control-lg"
                            required>
                    </td>

                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-lg removeRow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

                $('#detailTable tbody').append(row);
                $('.select2-product').last().select2({
                    width: "100%"
                });

                detailIndex++;
            });

            // ==============================
            // Hapus baris
            // ==============================
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@stop
