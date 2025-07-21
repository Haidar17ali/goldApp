@extends('adminlte::page')

@section('title', 'Ubah DP')
@php
    $nopol =
        old('nopol') ??
        ($down_payment->details->first()->nopol ?? ($down_payment->parent?->details->first()->nopol ?? ''));
@endphp

@section('content_header')
    <h1>Ubah DP</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="badge badge-primary float-right">Ubah DP</div>
        </div>
    </div>
    <form action="{{ route('down-payment.update', ['id' => $down_payment->id, 'type' => $type]) }}" method="POST">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="dp_type" class="col-sm-2 col-form-label">Jenis DP</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="dp_type" id="dp_type">
                                    <option>Silahkan Pilih</option>
                                    @foreach ($dp_types as $dp_type)
                                        <option {{ $down_payment->dp_type == $dp_type ? 'selected' : '' }}
                                            value="{{ $dp_type }}">{{ $dp_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nota_date" class="col-sm-2 col-form-label">Tanggal Nota</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="nota_date" name="nota_date"
                                    value="{{ old('nota_date', $down_payment->nota_date) }}">
                                <span class="text-danger error-text" id="nota_date_error"></span>
                            </div>
                            <label for="date" class="col-sm-2 col-form-label">Tanggal DP</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ old('date', $down_payment->date) }}">
                                <span class="text-danger error-text" id="date_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="supplier">
                                    <option>Silahkan Pilih</option>
                                    @foreach ($suppliers as $supplier)
                                        <option {{ $down_payment->supplier_id == $supplier->id ? 'selected' : '' }}
                                            value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="supplier"
                                    value="{{ old('supplier', $down_payment->supplier_id) }}" id="supplier_hidden">
                            </div>
                            <label for="nopol" class="col-sm-2 col-form-label">Nopol</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="nopol" name="nopol"
                                    value="{{ old('nopol', $nopol) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nominal" class="col-sm-2 col-form-label">Nominal</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="nominal" name="nominal"
                                    value="{{ old('nominal', $down_payment->nominal) }}">
                            </div>
                            <label for="pph" class="col-sm-2 col-form-label">PPH 22</label>
                            <div class="col-sm-4">
                                <input type="number" readonly class="form-control" id="pph" name="pph"
                                    value="{{ old('pph') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="arrival_date" class="col-sm-2 col-form-label">Tanggal Kedatangan</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="arrival_date" name="arrival_date"
                                    value="{{ old('arrival_date', $down_payment->arrival_date) }}">
                                <span class="text-danger error-text" id="arrival_date_error"></span>
                            </div>
                        </div>

                        <!-- Jika DP -->
                        <h3 id="data-detail">Data</h3>
                        <hr>
                        <!-- Jika Pelunasan -->
                        <div id="form-pelunasan" style="display: none;">
                            <div class="form-group row">
                                <label for="dp_select" class="col-sm-2 col-form-label">DP</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="dp_id" id="dp_select">
                                        <option value="">Silahkan Pilih DP</option>
                                        @foreach ($down_payments as $dp)
                                            <option {{ $down_payment->parent_id == $dp->id ? 'selected' : '' }}
                                                value="{{ $dp->id }}">
                                                {{ $dp->date }} | {{ $dp->supplier?->name ?? '' }} |
                                                {{ $dp->details?->first()->nopol ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="form-dp" style="display: none;">
                            @foreach ($detail_inputs as $index => $detail)
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Panjang ({{ $detail['length'] }})</label>
                                    <div class="col-sm-4">
                                        <input type="number" readonly class="form-control"
                                            value="{{ $detail['length'] }}" name="length[]">
                                    </div>

                                    <label class="col-sm-2 col-form-label">Jumlah</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="qty[]"
                                            value="{{ old('qty.' . $index, $detail['qty']) }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Kubikasi</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="cubication[]"
                                            value="{{ old('cubication.' . $index, $detail['cubication']) }}">
                                    </div>

                                    <label class="col-sm-2 col-form-label">Harga</label>
                                    <div class="col-sm-4">
                                        <input type="number" id="price-{{ $index }}" class="form-control"
                                            name="price[]" value="{{ old('price.' . $index, $detail['price']) }}">
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>


                        <div class="float-right mt-3">
                            <a href="{{ route('down-payment.index', $type) }}"
                                class="btn btn-danger rounded-pill mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill" id="btn-save">Simpan
                                Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        /*  */
    </style>
@stop

@section('js')
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>
    <script>
        $(document).ready(function() {
            var status = "{{ session('status') }}";
            console.log(status);

            if (status == "msgError") {
                Toastify({
                    text: "Minimal satu baris detail harus diisi dengan lengkap.",
                    className: "danger",
                    close: true,
                    style: {
                        background: "red",
                    }
                }).showToast();
            }

            function resetFormDP() {
                $('form :input').not('#dp_type, #dp_select, #btn-save, [name="_token"]').each(function() {
                    if ($(this).is(':checkbox') || $(this).is(':radio')) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).val('');
                    }

                    // Ganti ini:
                    // $(this).prop('readonly', false);
                    // $(this).prop('disabled', false);

                    // Dengan ini:
                    $(this).removeAttr('readonly').removeAttr('disabled');
                });

                $("#form-dp").find("input, select, textarea").each(function() {
                    if (!$(this).is('[name="_token"]')) {
                        $(this).val('').removeAttr('readonly').removeAttr('disabled');
                    }
                });

                // Khusus PPH tetap readonly
                $("#pph").val('').attr('readonly', true);

                // Reset select
                $('#supplier').val(null).trigger('change');
                $('#supplier_hidden').val('');
                $('#dp_select').val(null).trigger('change');
            }


            function disableForPelunasan() {
                $('form :input').each(function() {
                    let id = $(this).attr('id');
                    let name = $(this).attr('name');
                    let tag = $(this).prop("tagName").toLowerCase();
                    let type = $(this).attr("type");

                    // Jangan ubah input penting seperti token dan tombol
                    if (
                        id === 'btn-save' ||
                        name === '_token' ||
                        name === '_method'
                    ) {
                        return; // skip
                    }

                    // Untuk select, tetap disable agar tidak bisa diganti
                    if (tag === "select") {
                        if (
                            id !== 'dp_select' &&
                            id !== 'dp_type'
                        ) {
                            $(this).prop('disabled', true);
                        }
                    } else {
                        // Untuk input biasa dan textarea, jadikan readonly
                        if (
                            id !== 'date' &&
                            id !== 'nota_date' &&
                            id !== 'arrival_date' &&
                            id !== 'dp_select' &&
                            id !== 'dp_type'
                        ) {
                            $(this).prop('readonly', true);
                        }
                    }
                });
            }


            function setData(url, data, value, idVal) {
                let jsonData = loadWithData(url, data);

                if (!jsonData) {
                    console.error("jsonData is undefined or null", jsonData);
                    return;
                }
                let arrival_date;
                if (jsonData.children.length > 0) {
                    arrival_date = jsonData.children[0].arrival_date;
                } else {
                    arrival_date = jsonData.arrival_date;
                }



                // set data
                $("#arrival_date").val(arrival_date);
                $("#date").val(jsonData.date);
                $("#nota_date").val(jsonData.nota_date);
                $("#nopol").val(jsonData.details[0].nopol);

                if (jsonData.details.length == 2) {
                    let pph = Math.floor((jsonData.details[0].price + jsonData.details[1].price) * 0.0025);

                    $("#pph").val(pph);
                    $("#nominal").val(jsonData.details[0].price + jsonData.details[1].price - pph - jsonData
                        .nominal)
                } else {
                    let pph = Math.floor((jsonData.details[0].price) * 0.0025);

                    $("#pph").val(pph);
                    $("#nominal").val(jsonData.details[0].price - pph - jsonData.nominal)
                }

                // Kosongkan form detail lama
                $("#form-dp").empty();

                if (jsonData.details.length > 0) {
                    jsonData.details.forEach(function(detail, index) {
                        let html = `
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Panjang (${detail.length})</label>
                                        <div class="col-sm-4">
                                            <input type="number" readonly class="form-control" name="length[]" value="${detail.length}">
                                        </div>
                                        <label class="col-sm-2 col-form-label">Jumlah</label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" name="qty[]" value="${detail.qty}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Kubikasi</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="cubication[]" value="${detail.cubication ?? ''}">
                                        </div>
                                        <label class="col-sm-2 col-form-label">Harga</label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" name="price[]" value="${detail.price}">
                                        </div>
                                    </div>
                                    <hr>
                                    `;

                        $("#form-dp").append(html);
                    });
                }

                if (value == 'Pelunasan') {
                    disableForPelunasan()
                }

                // Set value supplier_id secara otomatis
                if (jsonData.supplier_id) {
                    $('#supplier_hidden').val(jsonData.supplier_id); // agar tetap terkirim
                    $('#supplier').prop('disabled', true);
                }
            }

            $('#supplier').select2({
                theme: "bootstrap4",
                tags: true
            });
            $('#dp_select').select2({
                theme: "bootstrap4",
                tags: true
            });

            $('#dp_type').on('change', function() {
                var value = $(this).val();
                // Reset semua input dan form
                if (value === 'DP') {
                    $('#form-dp').show();
                    $('#form-pelunasan').hide();
                    // resetFormDP();

                } else if (value === 'Pelunasan') {
                    $('#form-dp').show();
                    $('#form-pelunasan').show();
                } else {
                    $('#form-dp').hide();
                    $('#form-pelunasan').hide();
                }
            });

            // Trigger on page load (jika form disimpan sebelumnya)
            $('#dp_type').trigger('change');

            $("#dp_select").on('change', function() {
                let value = $('#dp_type').val();
                let idVal = $(this).val();
                let url = "{{ route('utility.npwpId') }}"
                let data = {
                    id: idVal,
                    model: 'Down_payment',
                    relation: ['supplier', "details", "children"],
                }

                if (value == "Pelunasan") {
                    setData(url, data, value, idVal);
                }

            })


            let value = $("#dp_type").val();

            if (value == "Pelunasan") {
                let idVal = $("#dp_select").val();
                if (idVal) {
                    let data = {
                        id: idVal,
                        model: 'Down_payment',
                        relation: ['supplier', "details", "children"],
                    };
                    let url = "{{ route('utility.npwpId') }}"
                    setData(url, data, value, idVal);
                    disableForPelunasan();
                }
            }

            $("#supplier").on("change", function() {
                let idSupp = $(this).val();
                $('#supplier_hidden').val(idSupp); // agar tetap terkirim
            })

            $('#price-0').on("input", function() {
                let price130 = parseFloat($(this).val()) || 0;
                let price1260 = parseFloat($("#price-1").val()) || 0;

                let pph = (price130 + price1260) * 0.0025;

                $("#pph").val(Math.floor(
                    pph)); // atau gunakan parseInt(pph) jika kamu memang ingin pembulatan ke bawah
            });

            $('#price-1').on("input", function() {
                let price130 = parseFloat($("#price-0").val()) || 0;
                let price1260 = parseFloat($(this).val()) || 0;

                let pph = (price130 + price1260) * 0.0025;

                $("#pph").val(Math.floor(pph));
            });

        });
    </script>
@stop
