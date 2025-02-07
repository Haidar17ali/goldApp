@extends('adminlte::page')

@section('title', 'Karyawan')

@section('content_header')
    <h1>Karyawan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('karyawan.buat') }}" class="btn btn-primary float-right" type="submit"><i
                    class="fas fa-plus"></i>
                Karyawan</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">NIP</th>
                        <th scope="col">Pin</th>
                        <th scope="col">NIK</th>
                        <th scope="col">Nama KTP</th>
                        <th scope="col">Nama Alias</th>
                        <th scope="col">Tanggal Masuk</th>
                        <th scope="col">Upah/Hari</th>
                        <th scope="col">Premi</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($employees))
                        @foreach ($employees as $employee)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $employee->nip }}</td>
                                <td>{{ $employee->pin }}</td>
                                <td>{{ $employee->nik }}</td>
                                <td>{{ $employee->fullname }}</td>
                                <td>{{ $employee->alias_name }}</td>
                                <td>{{ $employee->entry_date }}</td>
                                <td>Rp.{{ money_format($employee->salary != null ? $employee->salary->salary : 0) }}</td>
                                <td>Rp.{{ money_format($employee->premi) }}</td>
                                <td>
                                    <a href="{{ route('karyawan.ubah', $employee->id) }}" class="badge badge-success"><i
                                            class="fas fa-pencil-alt"></i></a>
                                    <form action="{{ route('karyawan.hapus', $employee->id) }}" class="d-inline"
                                        id="delete{{ $employee->id }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" data-id="{{ $employee->id }}"
                                            class="badge badge-pill badge-delete badge-danger d-inline">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center"><b>Data Karyawan tidak ditemukan!</b></td>
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
    <script>
        // toast
        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "saved") {
                Toastify({
                    text: "Permission baru berhasil ditambahkan!",
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
    </script>
@stop
