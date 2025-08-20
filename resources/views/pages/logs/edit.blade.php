@extends('adminlte::page')

@section('title', 'Ubah Log ' . $type)

@section('content_header')
    <h1>Ubah Log {{ $type }}</h1>
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
            <div class="badge badge-primary float-right">Ubah Log {{ $type }}</div>
        </div>
    </div>
    <form action="{{ route('log.update', ['type' => $type, 'id' => $log->id]) }}" method="POST">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if ($type == 'Merbau')
                            <div class="form-group row">
                                <label for="id_produksi" class="col-sm-2 col-form-label">ID Produksi</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="id_produksi" name="id_produksi"
                                        value="{{ old('id_produksi', $log->id_produksi) }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="barcode" class="col-sm-2 col-form-label">Barcode</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="barcode" name="barcode"
                                        value="{{ old('barcode', $log->barcode) }}">
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="quality" class="col-sm-2 col-form-label">Kualitas</label>
                            <div class="col-sm-10">
                                <select name="quality" class="form-control @error('quality') is-invalid @enderror"
                                    id="quality">
                                    <option selected value="null">Silahkan Pilih Kualitas</option>
                                    @foreach ($qualities as $quality)
                                        <option {{ $quality == $log->quality ? 'selected' : '' }}
                                            value="{{ $quality }}">
                                            {{ $quality }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="length" class="col-sm-2 col-form-label">Panjang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="length" name="length"
                                    value="{{ old('length', $log->length) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="diameter" class="col-sm-2 col-form-label">Diameter</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="diameter" name="diameter"
                                    value="{{ old('diameter', $log->diameter) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="quantity" class="col-sm-2 col-form-label">Jumlah</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="quantity" name="quantity"
                                    value="{{ old('quantity', $log->quantity) }}">
                            </div>
                        </div>
                        <div class="float-right mt-3">
                            <a href="{{ route('log.index', $type) }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
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
    <script>
        $(document).ready(function() {
            $('#address').select2({
                theme: "bootstrap-5",
                tags: true
            });
            $('#position').select2({
                theme: "bootstrap-5",
            });
            $('#number_account').select2({
                theme: "bootstrap-5",
                tags: true
            });

            $("#payment_type").on("change", function() {
                if ($(this).val() == "ATM") {
                    $('#bank_name').prop('disabled', false);
                    $('#bank_account').prop('disabled', false);
                    $('#number_account').prop('disabled', false);
                } else {
                    $('#bank_name').prop('disabled', true);
                    $('#bank_account').prop('disabled', true);
                    $('#number_account').prop('disabled', true);
                }
            })

            $("#address").on('select2:select', function() {
                let idAddress = $(this).val();

                $.ajax({
                    url: "{{ route('karyawan.alamat') }}",
                    type: "GET",
                    data: {
                        id: idAddress
                    },
                    success: function(response) {
                        if (response != null) {
                            $("#rt").val(response.rt);
                            $("#rw").val(response.rw);
                            $("#kelurahan").val(response.kelurahan);
                            $("#kecamatan").val(response.kecamatan);
                            $("#city").val(response.city);
                        } else {
                            $("#rt").val("");
                            $("#rw").val("");
                            $("#kelurahan").val("");
                            $("#kecamatan").val("");
                            $("#city").val("");
                        }

                    }
                })
            })
        });
    </script>
@stop
