@extends('adminlte::page')

@section('title', 'Gold Merge Conversion')

@section('content_header')
    <h1>Gold Merge Conversion</h1>
@stop

@section('content')
    <form action="{{ route('keluar-etalase.simpan') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">

                <table class="table table-bordered" id="conversionTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Stok</th>
                            <th>#</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <select name="details[0][product_variant_id]" class="form-control product select2">
                                    <option value="">-- pilih --</option>
                                    @foreach ($productVariants as $pv)
                                        <option value="{{ $pv->id }}">
                                            {{ $pv->product->name . '-' . $pv->karat->name . '-' . $pv->gram . 'g' . ' (' . $pv->type . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" name="details[0][qty]" class="form-control qty" value="1"
                                    min="1">
                            </td>

                            <td>
                                <div class="text-muted stock-info">
                                    Qty: <span id="qty-info">-</span>
                                </div>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row">✖</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm" id="addRow">
                    + Tambah Baris
                </button>

            </div>

            <div class="text-right card-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        let index = 1;

        $(".select2").select2({
            theme: "bootstrap-5",
            width: '100%'
        });

        // tambah baris
        $('#addRow').click(function() {

            let row = `
    <tr>
        <td>
            <select name="details[${index}][product_variant_id]"
                    class="form-control product select2">
                <option value="">-- pilih --</option>
                @foreach ($productVariants as $pv)
                    <option value="{{ $pv->id }}">{{ $pv->product->name }}-{{ $pv->karat->name }}-{{ $pv->gram }} -{{ $pv->type }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number"
                   name="details[${index}][qty]"
                   class="form-control qty"
                   value="1" min="1">
        </td>
        
        <td>
            <div class="text-muted stock-info">
                Qty: <span id="qty-info">-</span>
            </div>
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-danger btn-sm remove-row">✖</button>
        </td>
    </tr>
    `;

            $('#conversionTable tbody').append(row);

            // 🔥 INIT SELECT2 UNTUK BARIS BARU
            $('#conversionTable tbody tr:last .select2').select2({
                theme: "bootstrap-5",
                width: '100%'
            });

            $('#conversionTable tbody tr:last .select2-weight').select2({
                width: '100%'
            });

            index++;
        });


        // hapus baris
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    </script>

    <script>
        function resetWeight(row) {
            row.find('.weight')
                .empty()
                .append('<option value="">-- pilih berat --</option>')
                .trigger('change');

            row.find('.stock-info').html('Qty tersedia: -');
        }

        function loadWeights(row) {

            let productId = row.find('.product').val();
            console.log(productId);


            if (!productId) {
                resetWeight(row);
                return;
            }

            // weightSel.prop('disabled', true);
            // resetWeight(row);

            $.get("{{ route('stock.berat') }}", {
                product_id: productId,
            }, function(weights) {
                let box = row.find('#qty-info');
                box.html(weights);

            }).fail(() => {
                alert('Gagal mengambil data berat');
            });
        }

        function fetchQty(row) {

            let productId = row.find('.product').val();
            let karatId = row.find('.karat').val();
            let weight = row.find('.weight').val();
            let box = row.find('.stock-info');

            if (!productId || !karatId || !weight) {
                box.html('Qty tersedia: -');
                return;
            }

            box.text('Checking stock...');

            $.get("{{ route('stock.info') }}", {
                product_id: productId,
                karat_id: karatId,
                weight: weight
            }, function(res) {

                if (!res.available) {
                    box.html('<span class="text-danger">Stok tidak tersedia</span>');
                    return;
                }

                box.html(`Qty tersedia: <strong>${res.qty}</strong>`);
            });
        }

        // ======================
        // EVENT LISTENER
        // ======================
        $(document).on('change', '.product', function() {
            loadWeights($(this).closest('tr'));
        });

        // init select2
        $(document).on('focus', '.select2-weight', function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    width: '100%'
                });
            }
        });
    </script>

@stop
