@extends('adminlte::page')

@section('title', 'Ubah Rekening ')

@section('content_header')
    <h1>Ubah Rekening</h1>
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
            <div class="badge badge-primary float-right">Ubah Rekening</div>
        </div>
    </div>
    <form action="{{ route('rekening.update', $rekening->id) }}" method="POST" id="formRP">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="bank_name" class="col-sm-2 col-form-label">Nama Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="bank_name" name="bank_name"
                                    value="{{ old('bank_name', $rekening->bank_name) }}">
                                <span class="text-danger error-text" id="code_error"></span>
                            </div>
                            <label for="account_number" class="col-sm-2 col-form-label">No Rek</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="account_number" name="account_number"
                                    value="{{ old('account_number', $rekening->account_number) }}">
                                <span class="text-danger error-text" id="account_number_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account_holder" class="col-sm-2 col-form-label">Nama Rekening</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="account_holder" name="account_holder"
                                    value="{{ old('account_holder', $rekening->account_holder) }}">
                                <span class="text-danger error-text" id="account_holder_error"></span>
                            </div><label for="is_active" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-4">
                                <select name="is_active" id="is_active" class="form-control select2">
                                    <option value="">-- Pilih Status --</option>
                                    <option {{ $rekening->is_active == '1' ? 'selected' : '' }} value="1">Aktif
                                    </option>
                                    <option {{ $rekening->is_active == '0' ? 'selected' : '' }} value="0">Non-aktif
                                    </option>
                                </select>
                                <span class="text-danger error-text" id="is_active_error"></span>
                            </div>
                        </div>
                        <div class="float-right mt-3">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
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
@stop
