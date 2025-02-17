@extends('adminlte::page')

@section('title', 'Supplier')

@section('content_header')
    <h1>Supplier</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if (session('import_errors'))
                <div class="alert alert-danger">
                    <h4>Kesalahan File Excel:</h4>
                    <ul>
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-success ml-2 float-right" data-toggle="modal" data-target="#importSupplier">
                <i class="fas fa-file-import"></i> Import Data Supplier
            </button>
            <a href="{{ route('supplier.buat') }}" class="btn btn-primary float-right" type="submit"><i
                    class="fas fa-plus"></i>
                Supplier</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">NIK</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Nama Rek</th>
                        <th scope="col">No Rek</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($suppliers))
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $supplier->nik }}</td>
                                <td>{{ $supplier->supplier_type }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->bank != null ? $supplier->bank->bank_account : '' }}</td>
                                <td>{{ $supplier->bank != null ? $supplier->bank->number_account : '' }}</td>
                                <td>
                                    <a href="{{ route('supplier.ubah', $supplier->id) }}" class="badge badge-success"><i
                                            class="fas fa-pencil-alt"></i></a>
                                    <form action="{{ route('supplier.hapus', $supplier->id) }}" class="d-inline"
                                        id="delete{{ $supplier->id }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" data-id="{{ $supplier->id }}"
                                            class="badge badge-pill badge-delete badge-danger d-inline">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center"><b>Data Supplier tidak ditemukan!</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal box untuk import karyawan --}}
    <!-- Modal -->
    <div class="modal fade" id="importSupplier" tabindex="-1" aria-labelledby="importKaryawan" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('supplier.import') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('post')
                        <div class="row">
                            <label for="file" class="col-sm-2 col-form-label">Excel</label>
                            <div class="col-sm-10">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file" id="file">
                                    <label class="custom-file-label" for="file">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init()
        })
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
            } else if (status == "importSuccess") {
                Toastify({
                    text: "Data karyawan berhasil diimport!",
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
    </script>
@stop
