@extends('adminlte::page')

@section('title', 'Ubah Role')

@section('content_header')
    <h1>Ubah Role</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="badge badge-success float-right">Ubah Role</div>
        </div>
        <div class="card-body">
            <form action="{{ route('role.update', $role->id) }}" method="POST">
                @csrf
                @method('patch')
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $role->name) }}">
                    </div>
                </div>
                {{-- pilih permission --}}
                <h3>Pilih Permission</h3>
                <hr>
                <div class="container row">
                    @if ($groupedPermissions->count())
                        @foreach ($groupedPermissions as $group => $permissions)
                            <div class="col-12 mb-3">
                                <h5 class="text-primary text-uppercase"><b>{{ ucfirst($group) }}</b></h5>
                                <div class="row">
                                    @foreach ($permissions as $permission)
                                        <div class="toggle-container col-md-3">
                                            <!-- Tombol Toggle -->
                                            <label class="switch">
                                                <input type="checkbox" value="{{ $permission->id }}"
                                                    id="{{ $permission->name }}" name="permissions[]"
                                                    {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                            <!-- Tulisan di samping tombol -->
                                            <label for="{{ $permission->name }}"
                                                class="toggle-text"><b>{{ $permission->name }}</b></label>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                            </div>
                        @endforeach
                    @else
                        <div class="mx-auto">Silahkan Generate Permission Terlebih Dahulu!</div>
                    @endif
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
