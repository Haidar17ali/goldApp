@extends('adminlte::page')

@section('title', 'Edit Mutasi Antar Cabang')

@section('content_header')
    <h1 class="fw-bold">Edit Mutasi Antar Cabang</h1>
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

            <form method="POST" action="{{ route('mutasi-stok.update', $mutasi->id) }}">

                @csrf
                @method('patch')

                {{-- ================= HEADER ================= --}}

                <h5 class="mb-3 fw-bold">
                    Informasi Mutasi
                </h5>

                <div class="mb-4 row g-3">

                    <div class="col-md-4">

                        <label class="fw-semibold">
                            Tanggal Mutasi
                        </label>

                        <input type="date" name="transfer_date" class="form-control form-control-lg"
                            value="{{ old('transfer_date', $mutasi->transfer_date->format('Y-m-d')) }}" required>

                    </div>

                    <div class="col-md-4">

                        <label class="fw-semibold">
                            Dari Cabang
                        </label>

                        <select name="from_branch_id" class="form-control form-control-lg select2" required>

                            <option value="">
                                -- pilih cabang --
                            </option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('from_branch_id', $mutasi->from_branch_id) == $branch->id)>

                                    {{ $branch->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="fw-semibold">
                            Ke Cabang
                        </label>

                        <select name="to_branch_id" class="form-control form-control-lg select2" required>

                            <option value="">
                                -- pilih cabang --
                            </option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('to_branch_id', $mutasi->to_branch_id) == $branch->id)>

                                    {{ $branch->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-12">

                        <label class="fw-semibold">
                            Catatan
                        </label>

                        <textarea name="note" rows="2" class="form-control form-control-lg" placeholder="Catatan">{{ old('note', $mutasi->note) }}</textarea>

                    </div>

                </div>

                <hr class="my-4">

                {{-- ================= DETAIL ================= --}}

                <h5 class="mb-3 fw-bold">

                    Detail Barang

                </h5>

                <div class="table-responsive">

                    <table class="table align-middle table-bordered" id="detailTable">

                        <thead class="text-center table-light">

                            <tr>

                                <th width="45%">
                                    Produk
                                </th>

                                <th width="15%">
                                    Stok
                                </th>

                                <th width="15%">
                                    Qty
                                </th>

                                <th width="5%">

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            {{-- akan diisi javascript --}}

                        </tbody>

                    </table>

                    <div class="mb-3 text-end">

                        <button type="button" class="btn btn-success btn-lg" id="addRow">

                            <i class="fas fa-plus"></i>

                            Tambah Baris

                        </button>

                    </div>

                </div>

                <div class="text-end">

                    <a href="{{ route('mutasi-stok.index') }}" class="btn btn-secondary btn-lg">

                        <i class="fas fa-arrow-left"></i>

                        Kembali

                    </a>

                    <button type="submit" class="px-5 btn btn-primary btn-lg">

                        <i class="fas fa-save"></i>

                        Update Mutasi

                    </button>

                </div>

            </form>

        </div>
    </div>

@stop

@section('js')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const variants = @json($productVariants);
        const oldTransferQty = @json($oldTransferQty);

        const details = @json(
            $mutasi->details->map(function ($d) {
                return [
                    'product_variant_id' => $d->product_variant_id,
        
                    'qty' => $d->qty,
                ];
            }));

        $(document).ready(function() {

            $('.select2').select2({
                width: '100%',
                theme: 'bootstrap-5',
            });

            function getFromBranch() {
                return $('select[name="from_branch_id"]').val();
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Produk Duplikat
            |--------------------------------------------------------------------------
            */

            function isDuplicateProduct(selectedValue, currentSelect) {

                let duplicate = false;

                $('.select2-product').not(currentSelect).each(function() {

                    if ($(this).val() == selectedValue) {
                        duplicate = true;
                        return false;
                    }

                });

                return duplicate;
            }

            /*
            |--------------------------------------------------------------------------
            | Update stok pada baris
            |--------------------------------------------------------------------------
            */

            function updateRowStock(select) {

                let option = select.find(':selected');

                let stocks = option.data('stocks');

                if (typeof stocks === 'string') {
                    stocks = JSON.parse(stocks);
                }

                let branchId = getFromBranch();

                let qty = 0;

                if (branchId && Array.isArray(stocks)) {

                    // cari stok berdasarkan cabang
                    let stock = stocks.find(function(item) {
                        return parseInt(item.branch_id) === parseInt(branchId);
                    });

                    if (stock) {
                        qty = parseInt(stock.quantity) || 0;
                    }
                }

                // Tambahkan qty mutasi lama jika sedang edit
                let variantId = select.val();

                if (
                    oldTransferQty &&
                    oldTransferQty.hasOwnProperty(variantId)
                ) {
                    qty += parseInt(oldTransferQty[variantId]) || 0;
                }

                let row = select.closest('tr');

                row.find('.stock-info').text(qty);

                row.find('.qty-input').attr('max', qty);
            }

            /*
            |--------------------------------------------------------------------------
            | Tambah Row
            |--------------------------------------------------------------------------
            */

            function addRow(detail = null) {

                const index = $('#detailTable tbody tr').length;

                const selectedId = detail ?
                    detail.product_variant_id :
                    '';

                const qty = detail ?
                    detail.qty :
                    1;

                const html = `

        <tr>

            <td>

                <select
                    name="details[${index}][product_variant_id]"
                    class="form-control form-control-lg select2-product"
                    required>

                    <option value="">-- pilih barang --</option>

                    ${variants.map(v => `

                                                        <option
                                                            value="${v.id}"
                                                            ${selectedId==v.id?'selected':''}
                                                            data-stocks='${JSON.stringify(v.stocks)}'>

                                                            ${v.barcode}
                                                            |
                                                            ${v.product.name}
                                                            -
                                                            ${v.karat.name}
                                                            -
                                                            ${v.gram} gr

                                                        </option>

                                                    `).join('')}

                </select>

            </td>

            <td class="text-center">

                <span class="badge bg-info stock-info">

                    -

                </span>

            </td>

            <td>

                <input
                    type="number"
                    min="1"
                    value="${qty}"
                    class="form-control form-control-lg qty-input"
                    name="details[${index}][qty]"
                    required>

            </td>

            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-danger removeRow">

                    <i class="fas fa-trash"></i>

                </button>

            </td>

        </tr>

        `;

                $('#detailTable tbody').append(html);

                let select = $('.select2-product').last();

                select.select2({

                    width: '100%',
                    theme: 'bootstrap-5'

                });

                if (selectedId !== '') {

                    updateRowStock(select);

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Load Data Lama
            |--------------------------------------------------------------------------
            */

            if (details.length > 0) {

                details.forEach(function(item) {

                    addRow(item);

                });

            } else {

                addRow();

            }

            /*
            |--------------------------------------------------------------------------
            | Tambah Baris
            |--------------------------------------------------------------------------
            */

            $('#addRow').click(function() {

                addRow();

            });

            /*
            |--------------------------------------------------------------------------
            | Hapus Baris
            |--------------------------------------------------------------------------
            */

            $(document).on('click', '.removeRow', function() {

                $(this).closest('tr').remove();

            });

            /*
            |--------------------------------------------------------------------------
            | Ganti Produk
            |--------------------------------------------------------------------------
            */

            $(document).on('change', '.select2-product', function() {

                let value = $(this).val();

                if (!value) return;

                if (isDuplicateProduct(value, this)) {

                    Swal.fire({

                        icon: 'warning',

                        title: 'Produk sudah dipilih',

                        text: 'Produk tersebut sudah ada pada baris lain.'

                    });

                    $(this).val('').trigger('change');

                    return;

                }

                updateRowStock($(this));

            });

            /*
            |--------------------------------------------------------------------------
            | Qty
            |--------------------------------------------------------------------------
            */

            $(document).on('input', '.qty-input', function() {

                let max = parseInt($(this).attr('max')) || 0;

                let val = parseInt($(this).val()) || 0;

                if (val > max) {

                    Swal.fire({

                        icon: 'warning',

                        title: 'Stok tidak mencukupi',

                        text: 'Qty melebihi stok.'

                    });

                    $(this).val(max);

                }

            });

            /*
            |--------------------------------------------------------------------------
            | Ganti Cabang
            |--------------------------------------------------------------------------
            */

            $('select[name="from_branch_id"]').change(function() {

                $('.select2-product').each(function() {

                    if ($(this).val()) {

                        updateRowStock($(this));

                    }

                });

            });

        });

        $(document).on('select2:open', function() {

            document.querySelector(
                '.select2-container--open .select2-search__field'
            ).focus();

        });
    </script>

@endsection
