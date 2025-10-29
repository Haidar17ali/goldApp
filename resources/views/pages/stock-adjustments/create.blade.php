@extends('adminlte::page')

@section('title', 'Stock Opname')

@section('content_header')
    <h1 class="fw-bold">Stock Opname</h1>
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

            <form id="opnameForm" method="POST" action="{{ route('opname.simpan') }}">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <input type="hidden" name="storage_location_id" value="{{ $locationId }}">

                <div class="mb-4 row">
                    <div class="col-md-4">
                        <label class="fw-semibold">Tanggal Opname</label>
                        <input type="date" name="date" class="form-control form-control-lg"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Cabang</label>
                        <input type="text" class="form-control form-control-lg" value="{{ $branch->name ?? '-' }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-semibold">Lokasi Penyimpanan</label>
                        <input type="text" class="form-control form-control-lg" value="{{ $location->name ?? '-' }}" readonly>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 fw-bold">Detail Stok</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-bordered" id="opnameTable">
                        <thead class="text-center table-light">
                            <tr>
                                <th style="width: 25%">Produk</th>
                                <th style="width: 10%">Karat</th>
                                <th style="width: 15%">Berat</th>
                                <th style="width: 15%">Gold Type</th>
                                <th style="width: 15%">Qty Sistem</th>
                                <th style="width: 15%">Qty Aktual</th>
                                <th style="width: 15%">Selisih</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody id="opnameTable">
                            @forelse($stocks as $i => $stock)
                                <tr>
                                    <td>
                                        <select name="details[{{ $i }}][product_id]"
                                                class="form-control form-control-lg select-product" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" {{ $p->id == $stock->product_id ? 'selected' : '' }}>
                                                    {{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select name="details[{{ $i }}][karat_id]"
                                                class="form-control form-control-lg select-karat" required>
                                            <option value="">-- Pilih Karat --</option>
                                            @foreach($karats as $k)
                                                <option value="{{ $k->id }}" {{ $k->id == $stock->karat_id ? 'selected' : '' }}>
                                                    {{ $k->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" name="details[{{ $i }}][weight]"
                                            class="form-control form-control-lg text-end weight-input"
                                            value="{{ $stock->weight ?? 0 }}">
                                    </td>

                                    <td>
                                        <select name="details[{{ $i }}][gold_type]"
                                                class="form-control form-control-lg select-goldtype" required>
                                            <option value="new" {{ $stock->type == 'new' ? 'selected' : '' }}>New</option>
                                            <option value="sepuh" {{ $stock->type == 'sepuh' ? 'selected' : '' }}>Sepuh</option>
                                            <option value="rosok" {{ $stock->type == 'rosok' ? 'selected' : '' }}>Rosok</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" name="details[{{ $i }}][system_qty]"
                                            class="form-control form-control-lg text-end" value="{{ $stock->quantity }}" readonly>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" name="details[{{ $i }}][actual_qty]"
                                            class="form-control form-control-lg text-end actual-qty" value="{{ $stock->quantity }}">
                                    </td>

                                    <td class="text-end difference">0</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-lg removeRow"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td>
                                        <select name="details[0][product_id]" class="form-control form-control-lg select-product" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select name="details[0][karat_id]" class="form-control form-control-lg select-karat" required>
                                            <option value="">-- Pilih Karat --</option>
                                            @foreach($karats as $k)
                                                <option value="{{ $k->id }}">{{ $k->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" name="details[0][weight]"
                                            class="form-control form-control-lg text-end weight-input"
                                            value="{{ $stock->weight ?? 0 }}">
                                    </td>

                                    <td>
                                        <select name="details[0][gold_type]" class="form-control form-control-lg select-goldtype" required>
                                            <option value="">-- Pilih Gold Type --</option>
                                            <option value="new">New</option>
                                            <option value="sepuh">Sepuh</option>
                                            <option value="rosok">Rosok</option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" name="details[0][system_qty]" class="form-control form-control-lg text-end" value="0" readonly>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" name="details[0][actual_qty]"
                                            class="form-control form-control-lg text-end actual-qty" value="0">
                                    </td>

                                    <td class="text-end difference">0</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-lg addRow"><i class="fas fa-plus"></i></button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mb-3 text-end">
                    <h5><strong>Total Item: <span id="totalItem">{{ count($stocks) ?: 1 }}</span></strong></h5>
                </div>

                <div class="text-end">
                    <button type="submit" class="px-5 btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Simpan Opname
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
<style>
    #opnameTable td {
        vertical-align: middle !important;
    }

    /* Samakan style select2 dengan form-control-lg */
    .select2-container--default .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 0.5rem !important;
        border: 1px solid #ced4da !important;
    }

    .select2-selection__rendered {
        line-height: 1.5rem !important;
        font-size: 1rem !important;
    }

    .select2-selection__arrow {
        height: 100% !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .form-control-lg {
        height: calc(2.875rem + 2px);
    }

    #opnameTable th, #opnameTable td {
        padding: 0.5rem;
    }
</style>
@stop


@section('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('#opnameTable tbody');
    const totalItemEl = document.getElementById('totalItem');

    const goldTypes = ['new', 'sepuh', 'rosok'];

    function refreshSelect2() {
        $('.select-product').select2({ width: '100%', tags: true });
        $('.select-karat').select2({ width: '100%', tags: true });
    }

    refreshSelect2();

    function recalcDiff(tr) {
        const sys = parseFloat(tr.querySelector('input[name*="[system_qty]"]').value) || 0;
        const act = parseFloat(tr.querySelector('input[name*="[actual_qty]"]').value) || 0;
        tr.querySelector('.difference').textContent = (act - sys).toFixed(3);
    }

    table.addEventListener('input', e => {
        if (e.target.classList.contains('actual-qty')) {
            recalcDiff(e.target.closest('tr'));
        }
    });

    table.addEventListener('click', e => {
        if (e.target.closest('.addRow')) {
            const rows = table.querySelectorAll('tr').length;
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                <select name="details[${rows}][product_id]" class="form-control form-control-lg select-product" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="details[${rows}][karat_id]" class="form-control form-control-lg select-karat" required>
                    <option value="">-- Pilih Karat --</option>
                    @foreach($karats as $k)
                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.001" name="details[${rows}][weight]"
                       class="form-control form-control-lg text-end weight-input" value="0">
            </td>
            <td>
                <select name="details[${rows}][gold_type]" class="form-control form-control-lg select-goldtype" required>
                    <option value="">-- Pilih Gold Type --</option>
                    <option value="new">New</option>
                    <option value="sepuh">Sepuh</option>
                    <option value="rosok">Rosok</option>
                </select>
            </td>
            <td>
                <input type="number" name="details[${rows}][system_qty]"
                       class="form-control form-control-lg text-end" value="0" readonly>
            </td>
            <td>
                <input type="number" step="0.001" name="details[${rows}][actual_qty]"
                       class="form-control form-control-lg text-end actual-qty" value="0">
            </td>
            <td class="text-end difference">0</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-lg removeRow"><i class="fas fa-trash"></i></button>
            </td>
        `;

            table.appendChild(newRow);
            refreshSelect2();
            totalItemEl.textContent = table.querySelectorAll('tr').length;
        }

        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
            totalItemEl.textContent = table.querySelectorAll('tr').length;
        }
    });

    $(document).on('change', '.select-product, .select-karat, .select-goldtype, .weight-input', function () {
    let row = $(this).closest('tr');
    let product_id = row.find('.select-product').val();
    let karat_id = row.find('.select-karat').val();
    let weight = row.find('.weight-input').val();
    let gold_type = row.find('.select-goldtype').val();

    // pastikan semua sudah terisi
    if (product_id && karat_id && weight && gold_type) {
        $.ajax({
            url: "{{ route('opname.dapatStock') }}",
            method: "GET",
            data: { 
                product_id: product_id, 
                karat_id: karat_id, 
                weight: weight, 
                gold_type: gold_type 
            },
            success: function (res) {
                // pastikan response JSON benar
                console.log(res); 
                if (res && res.system_qty !== undefined) {
                    row.find('[name*="[system_qty]"]').val(res.system_qty);
                    updateDifference(row);
                } else {
                    row.find('[name*="[system_qty]"]').val(0);
                    row.find('.difference').text('0');
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                row.find('[name*="[system_qty]"]').val(0);
                row.find('.difference').text('0');
            }
        });
    }
});


    $(document).on('input', '.actual-qty', function () {
        let row = $(this).closest('tr');
        updateDifference(row);
    });

    function updateDifference(row) {
        let system_qty = parseFloat(row.find('[name*="[system_qty]"]').val()) || 0;
        let actual_qty = parseFloat(row.find('[name*="[actual_qty]"]').val()) || 0;
        let diff = (actual_qty - system_qty).toFixed(3);
        row.find('.difference').text(diff);
    }

});
</script>
@stop

