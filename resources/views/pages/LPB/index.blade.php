@extends('adminlte::page')

@section('title', 'LPB')

@section('content_header')
    <h1>LPB</h1>
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
            <a href="{{ route('lpb.buat') }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i>
                LPB</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal LPB</th>
                        <th scope="col">Tanggal Kedatangan</th>
                        <th scope="col">No Kitir</th>
                        <th scope="col">Nopol</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">NPWP</th>
                        <th scope="col">Grader & Tally</th>
                        <th scope="col">Status</th>
                        <th scope="col">Penyetuju</th>
                        @if (Auth::id() == 1)
                            <th scope="col">Pembuat</th>
                            <th scope="col">Pengedit</th>
                        @endif
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($lpbs))
                        @foreach ($lpbs as $lpb)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ date('d-m-Y', strtotime($lpb->lpb_date)) }}</td>
                                <td>{{ $lpb->roadPermit != null ? date('d-m-Y', strtotime($lpb->roadPermit->date)) : '' }}
                                </td>
                                <td>{{ $lpb->no_kitir }}</td>
                                <td>{{ $lpb->nopol }}</td>
                                <td>{{ $lpb->supplier != null ? $lpb->supplier->name : '' }}</td>
                                <td>{{ $lpb->npwp != null ? $lpb->npwp->name : '' }}</td>
                                <td>{{ $lpb->grader != null ? $lpb->grader->fullname : '' }} &
                                    {{ $lpb->tally != null ? $lpb->tally->fullname : '' }}
                                </td>
                                <td>
                                    <span
                                        class="badge {{ ($lpb->status == 'Sukses' ? 'badge-success' : $lpb->status == 'Pending') ? 'badge-warning' : 'badge-danger' }}">{{ $lpb->status }}</span>
                                </td>
                                <td>{{ $lpb->approvalBy != null ? $lpb->approvalBy->username : 'Menunggu Persetujuan' }}
                                </td>
                                @if (Auth::id() == 1)
                                    <td>{{ $lpb->createdBy != null ? $lpb->createdBy->username : '' }}</td>
                                    <td>{{ $lpb->edit_by != null ? $lpb->edit_by->username : '' }}</td>
                                @endif
                                <td>
                                    @if ($lpb->approvalBy == null)
                                        <a href="{{ route('lpb.ubah', $lpb->id) }}"
                                            class="badge badge-sm badge-success">Setujui</a>
                                        <a href="{{ route('lpb.ubah', $lpb->id) }}"
                                            class="badge badge-sm badge-danger">Tolak</a>
                                    @endif
                                    <a href="{{ route('lpb.ubah', $lpb->id) }}" class="badge badge-success"><i
                                            class="fas fa-pencil-alt"></i></a>
                                    <form action="{{ route('lpb.hapus', $lpb->id) }}" class="d-inline"
                                        id="delete{{ $lpb->id }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" data-id="{{ $lpb->id }}"
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
