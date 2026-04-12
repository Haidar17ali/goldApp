@extends('adminlte::page')

@section('title', 'Ubah Data Pengguna')

@section('content_header')
    <h1>Ubah Data Pengguna</h1>
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

            <div class="float-right badge badge-warning">Ubah Data Pengguna</div>
        </div>
    </div>

    <form action="{{ route('profil.update', $user->id) }}" method="POST" id="formRP">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        {{-- ================= DATA AKUN ================= --}}
                        <h5 class="mb-3">Data Akun</h5>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="username"
                                    value="{{ old('username', $user->username) }}">
                            </div>

                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-4">
                                <input type="email" class="form-control" name="email"
                                    value="{{ old('email', $user->email) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Password (Opsional)</label>
                            <div class="col-sm-4">
                                <input type="password" class="form-control" name="password">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti password</small>
                            </div>

                            <label class="col-sm-2 col-form-label">Konfirmasi</label>
                            <div class="col-sm-4">
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        @role(['super-admin'])
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="role_id[]" multiple id="role_id">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ in_array($role->name, $user->getRoleNames()->toArray()) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Aktivasi</label>
                                <div class="col-md-3">
                                    <label class="switch">
                                        <input type="checkbox" name="activation" {{ $user->is_active ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        @endrole

                        <hr>

                        {{-- ================= DATA PROFIL ================= --}}
                        <h5 class="mb-3">Data Profil Pegawai</h5>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">NIP</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nip" readonly
                                    value="{{ old('nip', $user->profile->nip ?? '') }}">
                            </div>

                            <label class="col-sm-2 col-form-label">NIK</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nik"
                                    value="{{ old('nik', $user->profile->nik ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nama"
                                    value="{{ old('nama', $user->profile->nama ?? '') }}">
                            </div>

                            <label class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="status" id="status">
                                    <option value="">Pilih</option>
                                    @foreach (['tk-0', 'tk-1', 'tk-2', 'tk-3', 'k-0', 'k-1', 'k-2', 'k-3'] as $st)
                                        <option value="{{ $st }}"
                                            {{ old('status', $user->profile->status ?? '') == $st ? 'selected' : '' }}>
                                            {{ ucfirst($st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="alamat">{{ old('alamat', $user->profile->alamat ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">No HP</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="no_hp"
                                    value="{{ old('no_hp', $user->profile->no_hp ?? '') }}">
                            </div>

                            <label class="col-sm-2 col-form-label">Cabang</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="branch_id" id="branch_id">
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id', $user->profile->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">No Rek</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="no_rek"
                                    value="{{ old('no_rek', $user->profile->no_rek ?? '') }}">
                            </div>

                            <label class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="nama_bank"
                                    value="{{ old('nama_bank', $user->profile->nama_bank ?? '') }}">
                            </div>
                        </div>


                        @role(['super-admin'])
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Gaji</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="gaji"
                                        value="{{ old('gaji', $user->profile->gaji ?? '') }}">
                                </div>
                            </div>
                        @endrole

                        <hr>

                        <div class="float-right mt-3">
                            <a href="{{ route('pengguna.index') }}" class="mr-2 btn btn-danger rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Perubahan</button>
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

@section('plugins.Toast', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>

    <script>
        $(document).ready(function() {

            // toast
            var status = "{{ session('status') }}";
            if (status == "edited") {
                Toastify({
                    text: "Data berhasil diubah!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            }

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
                theme: "bootstrap-5"
            });
            $('#branch_id').select2({
                theme: "bootstrap-5"
            });
            $('#status').select2({
                theme: "bootstrap-5"
            });

        });
    </script>
@stop
