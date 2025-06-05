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
                @foreach ($positions as $division)
                    <!-- Divisi -->
                    <span class="list-group-item d-flex justify-content-between align-items-center"
                        onclick="toggleList(event, 'divisi-{{ $division->id }}')">
                        <a href="#" style="color: #000">{{ $division->name }}</a>
                        <span>
                            <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                            <form class="d-inline" method="POST">
                                <button class="badge badge-danger border-0"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </span>
                    </span>
                    <div id="divisi-{{ $division->id }}" class="ml-3 d-none">
                        @foreach ($division->children as $department)
                            <!-- Departemen -->
                            <span class="list-group-item d-flex justify-content-between align-items-center"
                                onclick="toggleList(event, 'departemen-{{ $department->id }}')">
                                <a href="#" style="color: #000">{{ $department->name }}</a>
                                <span>
                                    <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                                    <form class="d-inline" method="POST">
                                        <button class="badge badge-danger border-0"><i class="fas fa-trash"></i>
                                            Delete</button>
                                    </form>
                                </span>
                            </span>
                            <div id="departemen-{{ $department->id }}" class="ml-4 d-none">
                                @foreach ($department->children as $section)
                                    <!-- Bagian -->
                                    <span class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $section->name }}
                                        <span>
                                            <a href="#" class="badge badge-success"><i class="fas fa-pencil-alt"></i>
                                                Edit</a>
                                            <form class="d-inline" method="POST">
                                                <button class="badge badge-danger border-0"><i class="fas fa-trash"></i>
                                                    Delete</button>
                                            </form>
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
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
            $(document).on('click', ".badge-delete", function(e) {
                e.preventDefault();
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
            function toggleList(event, id) {
                event.preventDefault(); // Mencegah link melakukan navigasi
                document.getElementById(id).classList.toggle('d-none');
            }
    </script>
@stop
