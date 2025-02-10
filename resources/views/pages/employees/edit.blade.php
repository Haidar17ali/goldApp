@extends('adminlte::page')

@section('title', 'Ubah Karyawan')

@section('content_header')
    <h1>Ubah Karyawan</h1>
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
            <div class="badge badge-primary float-right">Ubah Karyawan</div>
        </div>
    </div>
    <form action="{{ route('karyawan.update', $employee->id) }}" method="POST">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="pin" class="col-sm-2 col-form-label">PIN</label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" id="pin" name="pin"
                                    value="{{ old('pin', $employee->pin) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik" class="col-sm-2 col-form-label">NIK*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="nik" name="nik"
                                    value="{{ old('nik', $employee->nik) }}">
                            </div>
                            <label for="no_kk" class="col-sm-2 col-form-label">No KK*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="no_kk" name="no_kk"
                                    value="{{ old('no_kk', $employee->no_kk) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fullname" class="col-sm-2 col-form-label">Nama Lengkap*</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    value="{{ old('fullname', $employee->fullname) }}">
                            </div>
                            <label for="alias_name" class="col-sm-2 col-form-label">Nama Alias*</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="alias_name" name="alias_name"
                                    value="{{ old('alias_name', $employee->alias_name) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gender" class="col-sm-2 col-form-label">Jenis Kelamin*</label>
                            <div class="col-sm-1">
                                <select class="form-control" name="gender" id="gender">
                                    @foreach ($genders as $gender)
                                        <option {{ $gender['value'] == $employee->gender ? 'selected' : '' }}
                                            value="{{ $gender['value'] }}">
                                            {{ $gender['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="mariage_status" class="col-sm-3 col-form-label">Status Pernikahan*</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="mariage_status" id="mariage_status">
                                    @foreach ($mariage_statuses as $mariage_status)
                                        <option {{ $mariage_status == $employee->mariage_status ? 'selected' : '' }}
                                            value="{{ $mariage_status }}">{{ $mariage_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="family_depents" class="col-sm-2 col-form-label">Tanggungan*</label>
                            <div class="col-sm-1">
                                <select class="form-control" name="family_depents" id="family_depents">
                                    @for ($i = 0; $i <= 3; $i++)
                                        <option {{ $i == $employee->family_depents ? 'selected' : '' }}
                                            value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="employee_type" class="col-sm-2 col-form-label">Jenis Karyawan*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="employee_type" id="employee_type">
                                    @foreach ($employee_types as $employee_type)
                                        <option {{ $employee_type == $employee->employee_type ? 'selected' : '' }}
                                            value="{{ $employee_type }}">{{ $employee_type }}</option>
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
                                        <option {{ $address->id == $employee->address_id ? 'selected' : '' }}
                                            value="{{ $address->id }}">{{ $address->address }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-2 col-form-label">RT</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rt" name="rt"
                                    value="{{ old('rt', $employee->address != null ? $employee->address->rt : '') }}">
                            </div>
                            <label for="rw" class="col-sm-2 col-form-label">RW</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="rw" name="rw"
                                    value="{{ old('rw', $employee->address != null ? $employee->address->rw : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelurahan" class="col-sm-2 col-form-label">Kelurahan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kelurahan" name="kelurahan"
                                    value="{{ old('kelurahan', $employee->address != null ? $employee->address->kelurahan : '') }}">
                            </div>
                            <label for="kecamatan" class="col-sm-2 col-form-label">Kecamatan</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                                    value="{{ old('kecamatan', $employee->address != null ? $employee->address->kecamatan : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="city" class="col-sm-2 col-form-label">Kab/Kota</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city', $employee->address != null ? $employee->address->city : '') }}">
                            </div>
                        </div>
                        <h3>Pekerjaan</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="position" class="col-sm-2 col-form-label">Bagian*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="position" id="position">
                                    <option>Silahkan Pilih Bagian</option>
                                    @foreach ($positions as $position)
                                        <option {{ $position['id'] == $employee->position_id ? 'selected' : '' }}
                                            value="{{ $position['id'] }}">
                                            {{ $position['name'] . ' [' . $position['grandparent'] . ']' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="entry_date" class="col-sm-2 col-form-label">Tanggal Masuk*</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="entry_date" name="entry_date"
                                    value="{{ old('entry_date', $employee->entry_date) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="salary" class="col-sm-2 col-form-label">Gaji*</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="salary" name="salary"
                                    value="{{ old('salary', $employee->salary != null ? $employee->salary->salary : 0) }}">
                            </div>
                            <label for="premi" class="col-sm-2 col-form-label">Premi</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="premi" name="premi"
                                    value="{{ old('premi', $employee->premi) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location" class="col-sm-2 col-form-label">Lokasi*</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="location" id="location">
                                    <option value="null">Silahkan Pilih Lokasi Kerja</option>
                                    @foreach ($locations as $location)
                                        <option {{ $location == $employee->location ? 'selected' : '' }}
                                            value="{{ $location }}">
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
                                        <option {{ $payment == $employee->payment_type ? 'selected' : '' }}
                                            value="{{ $payment }}">
                                            {{ $payment }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="number_account" class="col-sm-2 col-form-label">No Rek</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="number_account" id="number_account">
                                    <option>Silahkan Isi No Rek Atau Pilih Rek</option>
                                    @foreach ($banks as $bank)
                                        <option {{ $bank->id == $employee->bank_id ? 'selected' : '' }}
                                            value="{{ $bank->id }}">{{ $bank->number_account }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_account" class="col-sm-2 col-form-label">Nama Rek</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" disabled id="bank_account"
                                    name="bank_account"
                                    value="{{ old('bank_account', $employee->bank != null ? $employee->bank->bank_account : '') }}">
                            </div>
                            <label for="bank_name" class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" disabled id="bank_name" name="bank_name"
                                    value="{{ old('bank_name', $employee->bank != null ? $employee->bank->bank_name : '') }}">
                            </div>
                        </div>
                        <h3>Lain-lain</h3>
                        <hr>
                        <div class="form-group row">
                            <label for="jkn_number" class="col-sm-2 col-form-label">No JKN</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="jkn_number" name="jkn_number"
                                    value="{{ old('jkn_number', $employee->jkn_number) }}">
                            </div>
                            <label for="jkp_number" class="col-sm-2 col-form-label">No JKP</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="jkp_number" name="jkp_number"
                                    value="{{ old('jkp_number', $employee->jkp_number) }}">
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
                                        <option {{ $status['value'] == $employee->status ? 'selected' : '' }}
                                            value="{{ $status['value'] }}">
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

            $('#number_account').select2({
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

            // set payment type
            if ("{{ $employee->payment_type }}" == "ATM") {
                $('#bank_name').prop('disabled', false);
                $('#bank_account').prop('disabled', false);
                $('#number_account').prop('disabled', false);
            } else {
                $('#bank_name').prop('disabled', true);
                $('#bank_account').prop('disabled', true);
                $('#number_account').prop('disabled', true);
            }

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

        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "addressErr") {
                Toastify({
                    text: "Data alamat tidak lengkap!",
                    className: "danger",
                    close: true,
                    style: {
                        background: "red",
                    }
                }).showToast();
            }
    </script>
@stop
