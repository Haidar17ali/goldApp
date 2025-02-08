@extends('adminlte::page')

@section('title', 'Buat Karyawan')

@section('content_header')
    <h1>Buat Karyawan</h1>
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
            <div class="badge badge-primary float-right">Buat Karyawan</div>
        </div>
    </div>
    <form action="{{ route('karyawan.buat') }}" method="POST">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="pin" class="col-sm-2 col-form-label">PIN</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" id="pin" name="pin"
                                    value="{{ old('pin') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik" class="col-sm-2 col-form-label">NIK*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="nik" name="nik"
                                    value="{{ old('nik') }}">
                            </div>
                            <label for="no_kk" class="col-sm-2 col-form-label">No KK*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="no_kk" name="no_kk"
                                    value="{{ old('no_kk') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fullname" class="col-sm-2 col-form-label">Nama Lengkap*</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    value="{{ old('fullname') }}">
                            </div>
                            <label for="alias_name" class="col-sm-2 col-form-label">Nama Alias*</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="alias_name" name="alias_name"
                                    value="{{ old('alias_name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gender" class="col-sm-2 col-form-label">Jenis Kelamin*</label>
                            <div class="col-sm-1">
                                <select class="form-control" name="gender" id="gender">
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender['value'] }}">{{ $gender['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="mariage_status" class="col-sm-3 col-form-label">Status Pernikahan*</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="mariage_status" id="mariage_status">
                                    @foreach ($mariage_statuses as $mariage_status)
                                        <option value="{{ $mariage_status }}">{{ $mariage_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="family_depents" class="col-sm-2 col-form-label">Tanggungan*</label>
                            <div class="col-sm-1">
                                <select class="form-control" name="family_depents" id="family_depents">
                                    @for ($i = 0; $i <= 3; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="employee_type" class="col-sm-2 col-form-label">Jenis Karyawan*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="employee_type" id="employee_type">
                                    @foreach ($employee_types as $employee_type)
                                        <option value="{{ $employee_type }}">{{ $employee_type }}</option>
                                    @endforeach
                                </select>
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
                                        <option value="{{ $address->id }}">{{ $address->address }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-2 col-form-label">RT</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rt" name="rt"
                                    value="{{ old('rt') }}">
                            </div>
                            <label for="rw" class="col-sm-2 col-form-label">RW</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rw" name="rw"
                                    value="{{ old('rw') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelurahan" class="col-sm-2 col-form-label">Kelurahan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kelurahan" name="kelurahan"
                                    value="{{ old('kelurahan') }}">
                            </div>
                            <label for="kecamatan" class="col-sm-2 col-form-label">Kecamatan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                                    value="{{ old('kecamatan') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-2 col-form-label">Kab/Kota</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city') }}">
                            </div>
                        </div>
                        <h3>Pekerjaan</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="position" class="col-sm-2 col-form-label">Bagian*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="position" id="position">
                                    <option value="null">Silahkan Pilih Bagian</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position['id'] }}">
                                            {{ $position['name'] . ' [' . $position['grandparent'] . ']' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="entry_date" class="col-sm-2 col-form-label">Tanggal Masuk*</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="entry_date" name="entry_date"
                                    value="{{ old('entry_date') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="salary" class="col-sm-2 col-form-label">Gaji*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="salary" name="salary"
                                    value="{{ old('salary') }}">
                            </div>
                            <label for="premi" class="col-sm-2 col-form-label">Premi</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="premi" name="premi"
                                    value="{{ old('premi') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location" class="col-sm-2 col-form-label">Lokasi*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="location" id="location">
                                    <option value="null">Silahkan Pilih Lokasi Kerja</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}">
                                            {{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <h3>Pembayaran</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="payment_type" class="col-sm-2 col-form-label">Jenis Pembayaran*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="payment_type" id="payment_type">
                                    <option value="null">Silahkan Pilih Jenis Pembayaran</option>
                                    @foreach ($payments as $payment)
                                        <option value="{{ $payment }}">
                                            {{ $payment }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="bank_name" class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" disabled id="bank_name" name="bank_name"
                                    value="{{ old('bank_name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_account" class="col-sm-2 col-form-label">Nama Rek</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" disabled id="bank_account"
                                    name="bank_account" value="{{ old('bank_account') }}">
                            </div>
                            <label for="number_account" class="col-sm-2 col-form-label">No Rek</label>
                            <div class="col-sm-4">
                                <input type="number" disabled class="form-control" id="number_account"
                                    name="number_account" value="{{ old('number_account') }}">
                            </div>
                        </div>
                        <h3>Lain-lain</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="jkn_number" class="col-sm-2 col-form-label">No JKN</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="jkn_number" name="jkn_number"
                                    value="{{ old('jkn_number') }}">
                            </div>
                            <label for="jkp_number" class="col-sm-2 col-form-label">No JKP</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="jkp_number" name="jkp_number"
                                    value="{{ old('jkp_number') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="status" class=" col-form-label">Status Karyawan</label>
                            <div class="col-sm-12">
                                <select class="form-control" name="status" id="status">
                                    <option value="null">Silahkan Pilih Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status['value'] }}">
                                            {{ $status['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="float-right mt-3">
                            <a href="{{ route('karyawan.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
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
