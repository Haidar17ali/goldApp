@extends('adminlte::page')

@section('title', 'Mutasi Antar Cabang')

@section('content_header')
    <h1 class="fw-bold">Tambah Mutasi Antar Cabang</h1>
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

            <form method="POST" action="{{ route('mutasi-stok.simpan') }}">
                @csrf

                {{-- ======================= HEADER ======================= --}}
                <h5 class="mb-3 fw-bold">Informasi Mutasi</h5>

                <div class="mb-4 row g-3">

                    <div class="col-md-4">
                        <label class="fw-semibold">Tanggal Mutasi</label>
                        <input type="date" name="transfer_date" class="form-control form-control-lg"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Dari Cabang</label>

                        <select name="from_branch_id" class="form-control form-control-lg select2" required>

                            <option value="">-- pilih cabang --</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Ke Cabang</label>

                        <select name="to_branch_id" class="form-control form-control-lg select2" required>

                            <option value="">-- pilih cabang --</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-12">

                        <label class="fw-semibold">Catatan</label>

                        <textarea name="note" rows="2" class="form-control form-control-lg" placeholder="Catatan (opsional)"></textarea>

                    </div>

                </div>

                <hr class="my-4">

                {{-- ======================= DETAIL ======================= --}}
                <h5 class="mb-3 fw-bold">Detail Barang</h5>

                <div class="table-responsive">

                    <table class="table align-middle table-bordered" id="detailTable">

                        <thead class="text-center table-light">

                            <tr>

                                <th width="40%">Produk</th>
                                <th width="15%">Stok</th>

                                <th width="15%">Qty</th>

                                <th width="5%"></th>

                            </tr>

                        </thead>

                        <tbody></tbody>

                    </table>

                    <div class="mb-3 text-end">

                        <button type="button" id="addRow" class="btn btn-success btn-lg">

                            <i class="fas fa-plus"></i>

                            Tambah Baris

                        </button>

                    </div>

                </div>

                <div class="text-end">

                    <button type="submit" class="px-5 btn btn-primary btn-lg">

                        <i class="fas fa-save"></i>

                        Simpan Mutasi

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

        $(document).ready(function() {

            $('.select2').select2({
                width: '100%',
                theme: "bootstrap-5",
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
            // function updateGrandTotal() {
            //     let total = 0;

            //     $('#detailTable tbody .weight').each(function() {
            //         total += parseFloat($(this).val()) || 0;
            //     });

            //     $('#grandTotalGram').text(
            //         total.toLocaleString('id-ID', {
            //             minimumFractionDigits: 3
            //         })
            //     );
            // }

            function isDuplicateProduct(selectedValue, currentSelect) {

                let duplicate = false;

                $('.select2-product').not(currentSelect).each(function() {

                    if ($(this).val() === selectedValue) {
                        duplicate = true;
                        return false; // break
                    }

                });

                return duplicate;
            }

            function updateRowStock(select) {

                let option = select.find(':selected');

                let stocks = option.data('stocks');

                if (typeof stocks === 'string') {
                    stocks = JSON.parse(stocks);
                }

                let branchId = getFromBranch();

                let qty = 0;

                if (branchId) {

                    let stock = stocks.find(s =>
                        parseInt(s.branch_id) == parseInt(branchId)
                    );

                    if (stock) {
                        qty = stock.quantity;
                    }

                }

                let row = select.closest('tr');

                row.find('.stock-info')
                    .text(qty);

                row.find('.qty-input')
                    .attr('max', qty);

            }

            $(document).on('change', '.select2-product', function() {

                let value = $(this).val();

                if (!value) return;

                if (isDuplicateProduct(value, this)) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk sudah dipilih',
                        text: 'Produk tersebut sudah ada pada baris sebelumnya.'
                    });

                    $(this).val('').trigger('change');
                    return;
                }

                updateRowStock($(this));
            });

            $(document).on('input', '.qty-input', function() {


                let max = parseInt($(this).attr('max')) || 0;

                let val = parseInt($(this).val()) || 0;

                if (val > max) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok tidak mencukupi',
                        text: 'Qty melebihi stok yang tersedia.'
                    });

                    $(this).val(max);

                }

            });

            $('select[name="from_branch_id"]').on('change', function() {

                $('.select2-product').each(function() {

                    if ($(this).val()) {
                        updateRowStock($(this));
                    }

                });

            });

            $(document).on('change', '.select2-product', function() {

                let value = $(this).val();

                if (!value) return;

                if (isDuplicateProduct(value, this)) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk sudah dipilih',
                        text: 'Produk tersebut sudah ada pada baris sebelumnya.'
                    });

                    $(this).val('').trigger('change');

                }

            });

            function getFromBranch() {
                return $('select[name="from_branch_id"]').val();
            }

            /* ================= TAMBAH BARIS ================= */
            function addRow() {

                const index = $('#detailTable tbody tr').length;

                const row = `
                            <tr>

                                <td>

                                    <select
                                        name="details[${index}][product_variant_id]"
                                        class="form-control form-control-lg select2-product"
                                        required>

                                        <option value="">-- pilih barang --</option>

                                        ${variants.map(v=>`

                                                                                        <option value="${v.id}" 
                                                                                        data-stocks='${JSON.stringify(v.stocks)}'>
                                                                                            ${v.barcode} |
                                                                                            ${v.product.name}
                                                                                            - ${v.karat.name}
                                                                                            - ${v.gram} gr
                                                                                        </option>

                                                                                    `).join('')}

                                    </select>

                                </td>

                                <td>
                                    <span class="badge bg-info stock-info">
                                        -
                                    </span>
                                </td>

                                <td>

                                    <input
                                        type="number"
                                        min="1"
                                        value="1"
                                        class="form-control form-control-lg qty-input"
                                        name="details[${index}][qty]"
                                        required>

                                </td>

                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-lg removeRow">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </td>

                            </tr>
                        `;

                $('#detailTable tbody').append(row);

                $('.select2-product').last().select2({
                    width: '100%',
                    theme: "bootstrap-5",
                });

            }

            /* ================= EVENT ================= */
            $('#addRow').on('click', addRow);

            // $(document).on('input', '.weight', updateGrandTotal);

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                // updateGrandTotal();
            });

            /* BARIS PERTAMA */
            addRow();
        });

        $(document).on('select2:open', () => {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        });

        // scroll wheel
        $(document).on('focus', '.weight', function() {
            $(this).on('wheel.disableScroll', function(e) {
                e.preventDefault();
            });
        });

        $(document).on('blur', '.weight', function() {
            $(this).off('wheel.disableScroll');
        });
    </script>

@stop
