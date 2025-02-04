@extends('adminlte::page')

@section('title', 'Buat Role')

@section('content_header')
    <h1>Buat Role</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="badge badge-primary float-right">Buat Role</div>
        </div>
        <div class="card-body">
            <form action="#">
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                    </div>
                </div>
                {{-- pilih permission --}}
                <h3>Pilih Permission</h3>
                <hr>
                <div class="container row">
                    @if (count($permissions))
                        @foreach ($permissions as $permission)
                            <div class="toggle-container col-md-3">
                                <!-- Tombol Toggle -->
                                <label class="switch">
                                    <input type="checkbox" value="{{ $permission->id }}" id="{{ $permission->name }}">
                                    <span class="slider round"></span>
                                </label>
                                <!-- Tulisan di samping tombol -->
                                <label for="{{ $permission->name }}"
                                    class="toggle-text"><b>{{ $permission->name }}</b></label>
                            </div>
                        @endforeach
                    @else
                        <div class="mx-auto">Silahkan Generate Permission Terlebih Dahulu!</div>
                    @endif
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
