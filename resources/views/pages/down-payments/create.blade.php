@extends('adminlte::page')

@section('title', 'Buat DP')

@section('content_header')
    <h1>Buat DP</h1>
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
            <div class="badge badge-primary float-right">Buat DP</div>
        </div>
    </div>
    <form action="{{ route('down-payment.simpan') }}" method="POST">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="supplier" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="supplier" id="supplier">
                                    <option>Silahkan Pilih</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nominal" class="col-sm-2 col-form-label">Nominal</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="nominal" name="nominal"
                                    value="{{ old('nominal') }}">
                            </div>
                        </div>
                        <div class="float-right mt-3">
                            <a href="{{ route('down-payment.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
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
            $('#supplier').select2({
                theme: "bootstrap4",
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
