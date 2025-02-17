@extends('adminlte::page')

@section('title', 'Set Pembongkar')

@section('content_header')
    <h1>Set Pembongkar</h1>
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

            <div id="error-messages"></div>
            <div class="badge badge-primary float-right">Set Pembongkar</div>
        </div>
    </div>
    <form action="{{ route('surat-jalan.update', ['id' => $road_permit->id, 'type' => $type]) }}" method="POST"
        id="formRP">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="handyman" class="col-sm-2 col-form-label">Pembongkar</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="handyman" id="handyman">
                                    {{-- @foreach ($trucks as $truck)
                                        <option value="{{ $truck }}">{{ $truck }}</option>
                                    @endforeach --}}
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="unpack_location" class="col-sm-2 col-form-label">Lokasi Bongkar</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="unpack_location" name="unpack_location"
                                    value="{{ old('unpack_location', $road_permit->unpack_location) }}">
                            </div>
                        </div>

                        <div class="float-right mt-3">
                            <a href="{{ route('surat-jalan.index', $type) }}"
                                class="btn btn-danger rounded-pill mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="loading" style="display: none;">
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <i class="fa fa-spinner fa-spin fa-3x"></i> <!-- Font Awesome spinner -->
            <p>Loading...</p>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <style>

    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
@stop
