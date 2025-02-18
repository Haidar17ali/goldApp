@extends('adminlte::page')

@section('title', 'Purchase Order' . $type)

@section('content_header')
    <h1>Purchase Order{{ $type }}</h1>
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
            <!-- Button trigger modal -->
            <a href="{{ route('purchase-order.buat', $type) }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i>
                Purchase Order {{ $type }}</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal PO</th>
                        <th scope="col">Kode PO</th>
                        <th scope="col">Supplier</th>
                        @if ($type == 'Bahan-Baku')
                            <th scope="col">Jenis Supplier</th>
                        @endif
                        <th scope="col">Status</th>
                        @if ($type != 'Bahan-Baku')
                            <th scope="col">PPN</th>
                            <th scope="col">DP</th>
                            <th scope="col">Pemesan</th>
                        @endif
                        <th scope="col">Pembuat</th>
                        <th scope="col">Pengedit</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($pos))
                        @foreach ($pos as $po)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ date('d-m-Y', strtotime($po->po_date)) }}</td>
                                <td>{{ $po->po_code }}</td>
                                <td>{{ $po->supplier != null ? $po->supplier->name : '' }}</td>
                                @if ($type == 'Sengon')
                                    <td>{{ $po->supplier_type }}</td>
                                @endif
                                <td>{{ $po->status }}</td>
                                @if ($type != 'Sengon')
                                    <td>{{ $po->ppn }}</td>
                                    <td>{{ $po->dp }}</td>
                                    <td>{{ $po->order_by != null ? $po->order_by->fullname : '' }}
                                @endif
                                <td>{{ $po->created_by != null ? $po->created_by->username : '' }}
                                <td>{{ $po->edited_by != null ? $po->edited_by->username : '' }}

                                </td>
                                <td>
                                    <a href="{{ route('purchase-order.ubah', ['type' => $type, 'id' => $po->id]) }}"
                                        class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                                    <form action="{{ route('purchase-order.hapus', $po->id) }}" class="d-inline"
                                        id="delete{{ $po->id }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" data-id="{{ $po->id }}"
                                            class="badge badge-pill badge-delete badge-danger d-inline">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14" class="text-center"><b>Data tidak ditemukan!</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
