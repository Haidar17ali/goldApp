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
    <th>Karat</th>
    <th>Berat (gr)</th>
    <th>Qty</th>
    <th>Stok</th>
    <th>#</th>
</tr>
</thead>

<tbody>
<tr>
    <td>
        <select name="details[0][product_id]" class="form-control product">
            <option value="">-- pilih --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </td>

    <td>
        <select name="details[0][karat_id]" class="form-control karat">
            <option value="">-- pilih --</option>
            @foreach ($karats as $karat)
                <option value="{{ $karat->id }}">{{ $karat->name }}</option>
            @endforeach
        </select>
    </td>
    
    <td>
        <select name="details[0][weight]"
                class="form-control form-control-lg weight select2-weight">
            <option value="">-- pilih berat --</option>
        </select>
    </td>

    <td>
        <input type="number"
               name="details[0][qty]"
               class="form-control qty"
               value="1"
               min="1">
    </td>


    <td>
        <div class="text-muted stock-info">
            Qty: -
            <br>Weight: -
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

<div class="card-footer text-right">
    <button type="submit" class="btn btn-success">Simpan</button>
</div>
</div>
</form>
@stop

@section('js')
<script>
let index = 1;

// tambah baris
$('#addRow').click(function () {

    let row = `
    <tr>
        <td>
            <select name="details[${index}][product_id]"
                    class="form-control product select2">
                <option value="">-- pilih --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <select name="details[${index}][karat_id]"
                    class="form-control karat select2">
                <option value="">-- pilih --</option>
                @foreach ($karats as $karat)
                    <option value="{{ $karat->id }}">{{ $karat->name }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <select name="details[${index}][weight]"
                    class="form-control weight select2-weight">
                <option value="">-- pilih berat --</option>
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
                Qty: -<br>Weight: -
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
        width: '100%'
    });

    $('#conversionTable tbody tr:last .select2-weight').select2({
        width: '100%'
    });

    index++;
});


// hapus baris
$(document).on('click', '.remove-row', function () {
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
    let karatId   = row.find('.karat').val();
    let weightSel = row.find('.weight');

    if (!productId || !karatId) {
        resetWeight(row);
        return;
    }

    weightSel.prop('disabled', true);
    resetWeight(row);

    $.get("{{ route('stock.berat') }}", {
        product_id: productId,
        karat_id: karatId
    }, function (weights) {

        if (weights.length === 0) {
            weightSel.append('<option value="">(stok kosong)</option>');
            return;
        }

        weights.forEach(w => {
            weightSel.append(`<option value="${w}">${w} gr</option>`);
        });

        weightSel.prop('disabled', false);
        weightSel.trigger('change');

    }).fail(() => {
        alert('Gagal mengambil data berat');
    });
}

function fetchQty(row) {

    let productId = row.find('.product').val();
    let karatId   = row.find('.karat').val();
    let weight    = row.find('.weight').val();
    let box       = row.find('.stock-info');

    if (!productId || !karatId || !weight) {
        box.html('Qty tersedia: -');
        return;
    }

    box.text('Checking stock...');

    $.get("{{ route('stock.info') }}", {
        product_id: productId,
        karat_id: karatId,
        weight: weight
    }, function (res) {

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
$(document).on('change', '.product, .karat', function () {
    loadWeights($(this).closest('tr'));
});

$(document).on('change', '.weight', function () {
    fetchQty($(this).closest('tr'));
});

// init select2
$(document).on('focus', '.select2-weight', function () {
    if (!$(this).hasClass('select2-hidden-accessible')) {
        $(this).select2({ width: '100%' });
    }
});
</script>

@stop
