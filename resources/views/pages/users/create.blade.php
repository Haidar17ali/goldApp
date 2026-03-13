@extends('adminlte::page')

@section('title', 'Buat Pengguna ')

@section('content_header')
    <h1>Buat Pengguna</h1>
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


            <div id="error-datas" style="color: red; margin-bottom: 10px;"></div>
            <div id="error-messages"></div>
            <div class="float-right badge badge-primary">Buat Pengguna</div>
        </div>
    </div>
    <form action="{{ route('pengguna.simpan') }}" method="POST" id="formRP">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body">

                            {{-- ================= USER ================= --}}
                            <h5 class="mb-3">Data Akun</h5>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="username"
                                        value="{{ old('username') }}">
                                </div>

                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password">
                                </div>

                                <label class="col-sm-2 col-form-label">Konfirmasi</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="role_id[]" multiple id="role_id">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            {{-- ================= PROFILE ================= --}}
                            <h5 class="mb-3">Data Profil Pegawai</h5>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">NIP</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="nip" value="{{ old('nip') }}">
                                </div>

                                <label class="col-sm-2 col-form-label">NIK</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="nik" value="{{ old('nik') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="nama" value="{{ old('nama') }}">
                                </div>

                                <label class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="status" id="status">
                                        <option value="">Pilih</option>
                                        <option value="tk-0">{{ ucwords('tk-0') }}</option>
                                        <option value="tk-1">{{ ucwords('tk-1') }}</option>
                                        <option value="tk-2">{{ ucwords('tk-2') }}</option>
                                        <option value="tk-3">{{ ucwords('tk-3') }}</option>
                                        <option value="k-0">{{ ucwords('k-0') }}</option>
                                        <option value="k-1">{{ ucwords('k-1') }}</option>
                                        <option value="k-2">{{ ucwords('k-2') }}</option>
                                        <option value="k-3">{{ ucwords('k-3') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="alamat">{{ old('alamat') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">No HP</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}">
                                </div>

                                <label class="col-sm-2 col-form-label">Cabang</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="branch_id" id="branch_id">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ ucwords($branch->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">No Rek</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="no_rek"
                                        value="{{ old('no_rek') }}">
                                </div>

                                <label class="col-sm-2 col-form-label">Nama Bank</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="nama_bank"
                                        value="{{ old('nama_bank') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Gaji</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="gaji"
                                        value="{{ old('gaji') }}">
                                </div>
                            </div>

                            <hr>

                            {{-- AKTIVASI --}}
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Aktivasi</label>
                                <div class="col-md-3">
                                    <label class="switch">
                                        <input type="checkbox" value="1" name="activation" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="float-right mt-3">
                                <a href="{{ route('pengguna.index') }}"
                                    class="mr-2 btn btn-danger rounded-pill">Batal</a>
                                <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <style>
        /* Mengubah warna header */
        .ht_clone_top th {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
        }

        /* Mengatur border tabel */
        .htCore {
            border-collapse: collapse;
            border: 2px solid #ddd;
        }

        /* Mengubah warna sel saat dipilih */
        .ht_master .current {
            background-color: #a3a3a3 !important;
        }

        /* Mengatur padding dan font */
        .htCore td,
        .htCore th {
            padding: 10px;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* toggle */

        /* Style untuk switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        /* Hide default checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Style untuk slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#role_id').select2({
                theme: "bootstrap-5",
            });
            $('#employee_id').select2({
                theme: "bootstrap-5",
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#role_id').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Role"
            });

            $('#branch_id').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Cabang"
            });

            $('#status').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Status"
            });

        });
    </script>
@stop
