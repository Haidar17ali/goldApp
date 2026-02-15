@extends('adminlte::page')

@section('title', 'Multi Barcode Print')

@section('content_header')
    <h1>Multi Barcode Print</h1>
@stop

@section('content')

    <div class="card card-outline card-primary">
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">

            {{-- SELECT VARIANT --}}
            <div class="form-group">
                <label>Pilih Product Variant</label>

                <select id="variantSelect" class="form-control" multiple>
                    @foreach ($variants as $v)
                        <option value="{{ $v->id }}" data-stock="{{ $v->stocks?->quantity ?? 0 }}">
                            {{ $v->product->name }}
                            {{ $v->karat?->name }}
                            {{ $v->gram }}g
                            {{ $v->barcode }}
                            Stock: {{ $v->stocks?->quantity ?? 0 }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TABLE SELECTED VARIANT --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Variant</th>
                            <th width="150">Qty Print</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>
                    <tbody id="variantTable"></tbody>
                </table>
            </div>

            {{-- FORM PRINT --}}
            <form method="POST" action="{{ route('barcode.cetak-form') }}" id="printForm" target="_blank">
                @csrf
                <div id="hiddenInputs"></div>

                <button class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Barcode
                </button>
            </form>

        </div>
    </div>

@stop


@section('css')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container .select2-selection--multiple {
            min-height: 38px;
        }
    </style>

@stop


@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {

            // ================= INIT SELECT2 =================
            $('#variantSelect').select2({
                placeholder: 'Pilih product variant'
            });

            $('#variantSelect').select2({
                placeholder: 'Pilih product variant',

                matcher: function(params, data) {

                    // kalau kosong tampil semua
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    if (!data.text) return null;

                    let search = params.term.toLowerCase().split(' ');
                    let text = data.text.toLowerCase();

                    let found = true;

                    search.forEach(word => {
                        if (!text.includes(word)) {
                            found = false;
                        }
                    });

                    return found ? data : null;
                }
            });


            // ================= ON SELECT =================
            $('#variantSelect').on('select2:select', function(e) {

                let option = $(e.params.data.element);

                let id = option.val();
                let text = option.text();
                let stock = parseInt(option.data('stock')) || 0;


                if ($('#row-' + id).length) return;

                let row = `
                    <tr id="row-${id}">
                        <td>${text}</td>

                        <td>
                            <input type="number"
                                class="form-control qty"
                                data-id="${id}"
                                value="${stock}"
                                min="1"
                                >
                        </td>

                        <td>
                            <button type="button"
                                class="btn btn-danger btn-sm remove"
                                data-id="${id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#variantTable').append(row);

            });


            // ================= REMOVE =================
            $(document).on('click', '.remove', function() {

                let id = $(this).data('id');

                $('#row-' + id).remove();

                // remove dari select2 juga
                let select = $('#variantSelect');
                let values = select.val().filter(v => v != id.toString());
                select.val(values).trigger('change');

            });


            // ================= SUBMIT =================
            $('#printForm').on('submit', function() {

                $('#hiddenInputs').html('');

                let index = 0;

                $('.qty').each(function() {

                    let id = $(this).data('id');
                    let qty = $(this).val();

                    if (qty <= 0) return;

                    $('#hiddenInputs').append(`
            <input type="hidden" name="variants[${index}][id]" value="${id}">
            <input type="hidden" name="variants[${index}][qty]" value="${qty}">
        `);

                    index++;
                });

            });


        });
    </script>

@stop
