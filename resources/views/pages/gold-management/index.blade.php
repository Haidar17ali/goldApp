@extends('adminlte::page')

@section('title', 'Pengelolaan Emas')

@section('content_header')
    <h1 class="mb-3">Daftar Pengelolaan Emas</h1>
@stop

@section('content')
    <div class="shadow-sm card">
        <div class="card-header">
            <h4 class="float-left mb-0">Riwayat Pengelolaan</h4>
            <a href="{{ route('pengelolaan-emas.buat') }}" class="float-right btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Tambah Pengelolaan
            </a>
        </div>

        <div class="p-5 card-body table-responsive">
            <table class="table mb-0 text-center align-middle table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Produk</th>
                        <th>Karat</th>
                        <th>Gram Masuk (Customer)</th>
                        <th>Gram Hasil</th>
                        <th>Susut</th>
                        <th>Catatan</th>
                        <th>Dibuat Oleh</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($managements as $i => $m)
                        <tr>
                            <td>{{ $managements->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->date)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $badgeClass =
                                        [
                                            'sepuh' => 'warning',
                                            'patri' => 'info',
                                            'rosok' => 'secondary',
                                        ][$m->type] ?? 'light';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($m->type) }}</span>
                            </td>
                            <td>{{ $m->product->name ?? '-' }}</td>
                            <td>{{ $m->karat->name ?? '-' }}</td>
                            <td>{{ number_format($m->gram_out, 3) }} gr</td>
                            <td>{{ number_format($m->gram_in, 3) }} gr</td>
                            <td>{{ number_format($m->gram_out - $m->gram_in, 3) }} gr</td>
                            <td>{{ $m->note ?? '-' }}</td>
                            <td>{{ $m->creator->username ?? '-' }}</td>
                            <td>
                                {{-- <a href="{{ route('pengelolaan-emas.show', $m->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a> --}}
                                <a href="{{ route('pengelolaan-emas.ubah', $m->id) }}"
                                    class="badge badge-sm badge-success">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pengelolaan-emas.hapus', $m->id) }}" class="d-inline"
                                    method="post">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#" data-id="{{ $m->id }}"
                                        class="badge badge-pill badge-delete badge-danger d-inline">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-3 text-muted">Belum ada data pengelolaan emas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $managements->links() }}
            </div>
        </div>
    </div>
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
            } else if (status == "used") {
                Toastify({
                    text: "Data LPB Terpakai Dan Stock Berkurang!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            }
            $(document).ready(function() {
                // delete 
                $(document).on("click", ".badge-delete", function(e) {
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
                        if (result.value) {
                            form.submit();
                        }
                    });
                });
            })
    </script>
@stop
