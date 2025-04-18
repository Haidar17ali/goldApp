@extends('adminlte::page')

@section('title', 'Ubah Supplier')

@section('content_header')
    <h1>Ubah Supplier</h1>
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
            <div class="badge badge-primary float-right">Ubah Supplier</div>
        </div>
    </div>
    <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="npwp_id" class="col-sm-2 col-form-label">NPWP</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="npwp_id" id="npwp_id">
                                    <option>Silahkan Pilih NPWP</option>
                                    @foreach ($npwps as $npwp)
                                        <option {{ $npwp->id == $supplier->npwp_id ? 'selected' : '' }}
                                            value="{{ $npwp->id }}">
                                            {{ $npwp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik" class="col-sm-2 col-form-label">NIK</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="nik" name="nik"
                                    value="{{ old('nik', $supplier->nik) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-sm-2 col-form-label">Jenis</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="type" id="type">
                                    @foreach ($types as $type)
                                        <option {{ $type == $supplier->supplier_type ? 'selected' : '' }}
                                            value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $supplier->name) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-sm-2 col-form-label">No Telp</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $supplier->no_telp) }}">
                            </div>
                        </div>
                        <h3>Alamat</h3>
                        <hr>

                        <div class="form-group row">
                            <label for="address" class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="address" id="address">
                                    <option>Silahkan Isi Alamat Atau Pilih Alamat</option>
                                    @foreach ($addresses as $address)
                                        <option {{ $supplier->address_id == $address->id ? 'selected' : '' }}
                                            value="{{ $address->id }}">{{ $address->address }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-2 col-form-label">RT</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rt" name="rt"
                                    value="{{ old('rt', $supplier->address != null ? $supplier->address->rt : '') }}">
                            </div>
                            <label for="rw" class="col-sm-2 col-form-label">RW</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rw" name="rw"
                                    value="{{ old('rw', $supplier->address != null ? $supplier->address->rw : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelurahan" class="col-sm-2 col-form-label">Kelurahan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kelurahan" name="kelurahan"
                                    value="{{ old('kelurahan', $supplier->address != null ? $supplier->address->kelurahan : '') }}">
                            </div>
                            <label for="kecamatan" class="col-sm-2 col-form-label">Kecamatan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                                    value="{{ old('kecamatan', $supplier->address != null ? $supplier->address->kecamatan : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-2 col-form-label">Kab/Kota</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city', $supplier->address != null ? $supplier->address->city : '') }}">
                            </div>
                        </div>
                        <h3>Pembayaran</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="number_account" class="col-sm-2 col-form-label">No Rek</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="number_account" id="number_account">
                                    <option>Silahkan Isi No Rek Atau Pilih Rek</option>
                                    @foreach ($banks as $bank)
                                        <option {{ $supplier->bank_id == $bank->id ? 'selected' : '' }}
                                            value="{{ $bank->id }}">{{ $bank->number_account }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_account" class="col-sm-2 col-form-label">Nama Rek</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="bank_account" name="bank_account"
                                    value="{{ old('bank_account', $supplier->bank != null ? $supplier->bank->bank_account : '') }}">
                            </div>
                            <label for="bank_name" class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="bank_name" name="bank_name"
                                    value="{{ old('bank_name', $supplier->bank != null ? $supplier->bank->bank_name : '') }}">
                            </div>
                        </div>
                        <div class="float-right mt-3">
                            <a href="{{ route('supplier.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
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
                theme: "bootstrap4",
                tags: true
            });
            $('#position').select2({
                theme: "bootstrap4",
            });
            $('#number_account').select2({
                theme: "bootstrap4",
                tags: true
            });
            $('#npwp_id').select2({
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
