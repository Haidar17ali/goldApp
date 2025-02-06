@extends('adminlte::page')

@section('title', 'Bagian')

@section('content_header')
    <h1>Divisi,Departemen & Bagian</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('bagian.buat') }}" class="btn btn-primary float-right" type="submit"><i class="fas fa-plus"></i>
                Divisi | Departemen | Bagian</a>
        </div>
        <div class="card-body">
            <div class="list-group">
                @if (count($positions))
                    @foreach ($positions as $position)
                        <span class="list-group-item d-flex justify-content-between align-items-center"
                            onclick="toggleList('{{ $position->name }}')">
                            <a href="#" style="color: #000">{{ $position->name }}</a>
                            <span>
                                <a href="{{ route('bagian.ubah', $position->id) }}" class="badge badge-success"><i
                                        class="fas fa-pencil-alt"></i>
                                    Edit</a>
                                <form action="{{ route('bagian.hapus', $position->id) }}" class="d-inline" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#" class="badge badge-danger badge-delete border-0"><i
                                            class="fas fa-trash"></i>
                                        Delete</a>
                                </form>
                            </span>
                        </span>
                        @if (count($position->children))
                            @foreach ($position->children as $department)
                                <div id="{{ $position->name }}" class="ml-3 d-none"
                                    onclick="toggleList('{{ $position->name . $department->name }}')">
                                    <span class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="#" style="color: #000">{{ $department->name }}</a>
                                        <span>
                                            <a href="{{ route('bagian.ubah', $department->id) }}"
                                                class="badge badge-success"><i class="fas fa-pencil-alt"></i>
                                                Edit</a>
                                            <form action="{{ route('bagian.hapus', $department->id) }}" class="d-inline"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="badge badge-danger badge-delete border-0"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</a>
                                            </form>
                                        </span>
                                    </span>
                                    @if (count($department->children))
                                        @foreach ($department->children as $subPosition)
                                            <div id="{{ $position->name . $department->name }}" class="ml-4 d-none">
                                                <span
                                                    class="list-group-item d-flex justify-content-between align-items-center">{{ $subPosition->name }}
                                                    <span>
                                                        <a href="{{ route('bagian.ubah', $subPosition->id) }}"
                                                            class="badge badge-success"><i class="fas fa-pencil-alt"></i>
                                                            Edit</a>
                                                        <form action="{{ route('bagian.hapus', $subPosition->id) }}"
                                                            class="d-inline" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#"
                                                                class="badge badge-danger badge-delete border-0"><i
                                                                    class="fas fa-trash"></i>
                                                                Delete</a>
                                                        </form>
                                                    </span>
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <div class="mx-auto">
                        <h3>
                            <b class="text-center">Data bagian tidak ditemukan!</b>
                        </h3>
                    </div>
                @endif

                {{-- <span class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="#" onclick="toggleList('divisi2')">Divisi 2</a>
                    <span>
                        <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                        <form class="d-inline" method="POST">
                            <button class="badge badge-danger border-0"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </span>
                </span>
                <div id="divisi2" class="ml-3 d-none">
                    <span class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="#" onclick="toggleList('departemen2')">Departemen 2</a>
                        <span>
                            <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                            <form class="d-inline" method="POST">
                                <button class="badge badge-danger border-0"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </span>
                    </span>
                    <div id="departemen2" class="ml-4 d-none">
                        <span class="list-group-item d-flex justify-content-between align-items-center">Bagian C
                            <span>
                                <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form class="d-inline" method="POST">
                                    <button class="badge badge-danger border-0"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </span>
                        </span>
                        <span class="list-group-item d-flex justify-content-between align-items-center">Bagian D
                            <span>
                                <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <form class="d-inline" method="POST">
                                    <button class="badge badge-danger border-0"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </span>
                        </span>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        // toast
        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "saved") {
                Toastify({
                    text: "Data baru berhasil ditambahkan!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == 'edited') {
                Toastify({
                    text: "Data berhasil diubah!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == 'deleted') {
                Toastify({
                    text: "Data berhasil dihapus!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == "none") {
                Toastify({
                    text: "Permision tidak ditemukan",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            }

            // delete
            $(".badge-delete").click(function(e) {
                var form = $(this).closest("form");
                Swal.fire({
                    title: 'Hapus Data!',
                    text: "Apakah anda yakin akan menghapus data ini?",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus!!'
                }).then((result) => {
                    if (result.value === true) {
                        form.closest("form").submit();
                    }
                })
            });

            // list gorup
            function toggleList(id) {
                let element = document.getElementById(id);
                if (element.classList.contains('d-none')) {
                    element.classList.remove('d-none');
                } else {
                    element.classList.add('d-none');
                }
            }
    </script>
@stop
