@extends('adminlte::page')

@section('title', 'DP')

@section('content_header')
    <h1>DP</h1>
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
            <a href="{{ route('down-payment.buat') }}" class="btn btn-primary float-right" type="submit"><i
                    class="fas fa-plus"></i>
                DP</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">Nominal</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($down_payments))
                        @foreach ($down_payments as $down_payment)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <th scope="row">{{ date('d-m-Y', strtotime($down_payment->date)) }}</th>
                                <td>{{ $down_payment->supplier != null ? $down_payment->supplier->name : 'supplier tidak ditemukan' }}
                                </td>
                                <td>Rp.{{ money_format($down_payment->nominal) }}</td>
                                <td style="color:{{ $down_payment->type == 'In' ? 'green' : 'red' }}"><i
                                        class="fas fa-arrow-{{ $down_payment->type == 'In' ? 'up' : 'down' }}"></i>{{ $down_payment->type }}
                                </td>
                                <td><span
                                        class="badge badge-{{ $down_payment->status == 'Pending' ? 'warning' : ($down_payment->status == 'Gagal' ? 'danger' : 'success') }}">{{ $down_payment->status }}</span>
                                </td>
                                <td>
                                    @if ($down_payment->status == 'Pending')
                                        <a href="{{ route('utility.activation-dp', ['modelType' => 'Down_payment', 'id' => $down_payment->id, 'status' => 'Menunggu Pembayaran']) }}"
                                            class="badge badge-success"><i class="fas fa-check"></i></a>
                                        <a href="{{ route('utility.activation-dp', ['modelType' => 'down_payment', 'id' => $down_payment->id, 'status' => 'Gagal']) }}"
                                            class="badge badge-danger"><i class="fas fa-times"></i></a>
                                        ||
                                        <a href="{{ route('down-payment.ubah', $down_payment->id) }}"
                                            class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                                        <form action="{{ route('down-payment.hapus', $down_payment->id) }}"
                                            class="d-inline" id="delete{{ $down_payment->id }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" data-id="{{ $down_payment->id }}"
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
                            <td colspan="7" class="text-center"><b>Data down-payment tidak ditemukan!</b></td>
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
