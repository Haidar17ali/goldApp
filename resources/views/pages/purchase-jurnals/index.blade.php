@extends('adminlte::page')

@section('title', 'Purchase Jurnal')

@section('content_header')
    <h1>Purchase Jurnal</h1>
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
            <a href="{{ route('purchase-jurnal.buat') }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i>
                Purchase Jurnal</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal Jurnal</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Total Rupiah</th>
                        <th scope="col">Total Terbayar</th>
                        <th scope="col">Total Gagal</th>
                        <th scope="col">Status</th>
                        @if (Auth::id() == 1)
                            <th scope="col">Pembuat</th>
                            <th scope="col">Pengedit</th>
                        @endif
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($purchase_jurnals))
                        @foreach ($purchase_jurnals as $purchase_jurnal)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ date('d-m-Y', strtotime($purchase_jurnal->date)) }}</td>
                                <td>{{ $purchase_jurnal->pj_code }}</td>
                                <td>{{ hitungTotalPembayaran($purchase_jurnal->allLpbs) }}</td>
                                <td>{{ hitungTotalPembayaran($purchase_jurnal->failedLpbs) }}</td>
                                <td>{{ count($purchase_jurnal->failedLpbs) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $purchase_jurnal->status == 'Selesai' ? 'badge-success' : 'badge-warning' }}">{{ $purchase_jurnal->status }}
                                    </span>
                                </td>
                                </td>
                                @if (Auth::id() == 1)
                                    <td>{{ $purchase_jurnal->createdBy != null ? $purchase_jurnal->createdBy->username : '' }}
                                    </td>
                                    <td>{{ $purchase_jurnal->edit_by != null ? $purchase_jurnal->edit_by->username : '' }}
                                    </td>
                                @endif
                                <td>
                                    @if ($purchase_jurnal->approved_by == null)
                                        <a href="{{ route('utility.approve-lpb', ['modelType' => 'LPB', 'id' => $purchase_jurnal->id, 'status' => 'Pending']) }}"
                                            class="badge badge-sm badge-success"><i class="fas fa-check"></i></a>
                                        <a href="{{ route('lpb.ubah', $purchase_jurnal->id) }}"
                                            class="badge badge-sm badge-danger"><i class="fas fa-times"></i></a>
                                        <a href="{{ route('purchase-jurnal.ubah', $purchase_jurnal->id) }}"
                                            class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                                        <form action="{{ route('purchase-jurnal.hapus', $purchase_jurnal->id) }}"
                                            class="d-inline" id="delete{{ $purchase_jurnal->id }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" data-id="{{ $purchase_jurnal->id }}"
                                                class="badge badge-pill badge-delete badge-danger d-inline">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </form>
                                    @endif
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
        localStorage.removeItem('collapseState');
        localStorage.removeItem('selectedLPBData');
        localStorage.removeItem('selectedLPBs');
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
