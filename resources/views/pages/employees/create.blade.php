@extends('adminlte::page')

@section('title', 'Buat Karyawan')

@section('content_header')
    <h1>Buat Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="badge badge-primary float-right">Buat Karyawan</div>
        </div>
        <div class="card-body">
            <form action="{{ route('karyawan.buat') }}" method="POST">
                @csrf
                @method('post')
                <div class="form-group row">
                    <label for="pin" class="col-sm-2 col-form-label">PIN</label>
                    <div class="col-sm-5">
                        <input type="number" class="form-control" id="pin" name="pin"
                            value="{{ old('pin') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nik" class="col-sm-2 col-form-label">NIK</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="nik" name="nik"
                            value="{{ old('nik') }}">
                    </div>
                    <label for="no_kk" class="col-sm-1 col-form-label">No KK</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="no_kk" name="no_kk"
                            value="{{ old('no_kk') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="fullname" class="col-sm-2 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="fullname" name="fullname"
                            value="{{ old('fullname') }}">
                    </div>
                    <label for="alias_name" class="col-sm-1 col-form-label">Nama Alias</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="alias_name" name="alias_name"
                            value="{{ old('alias_name') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Gender" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                    <div class="col-sm-5">
                        <input type="number" class="form-control" id="pin" name="pin"
                            value="{{ old('pin') }}">
                    </div>
                </div>
                <div class="float-right mt-3">
                    <a href="{{ route('role.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
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
    <script>
        // toast
    </script>
@stop
