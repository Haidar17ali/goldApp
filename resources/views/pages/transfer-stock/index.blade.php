@extends('adminlte::page')

@section('title', 'Mutasi Antar Cabang')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mutasi Antar Cabang</h1>

        <a href="{{ route('mutasi-stok.buat') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Mutasi
        </a>
    </div>
@stop

@section('content')

    <div class="card">

        <div class="p-0 card-body">

            <div class="mb-3 card">

                <div class="card-body">

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-2">
                                <label>Dari Tanggal</label>

                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label>Sampai</label>

                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-2">

                                <label>Dari Cabang</label>

                                <select name="from_branch_id" class="form-control">

                                    <option value="">Semua</option>

                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(request('from_branch_id') == $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Ke Cabang</label>

                                <select name="to_branch_id" class="form-control">

                                    <option value="">Semua</option>

                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(request('to_branch_id') == $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Status</label>

                                <select name="status" class="form-control">

                                    <option value="">Semua</option>

                                    <option value="draft" @selected(request('status') == 'draft')>
                                        Draft
                                    </option>

                                    <option value="sent" @selected(request('status') == 'sent')>
                                        Dikirim
                                    </option>

                                    <option value="received" @selected(request('status') == 'received')>
                                        Diterima
                                    </option>

                                    <option value="cancelled" @selected(request('status') == 'cancelled')>
                                        Batal
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-2">

                                <label>Keyword</label>

                                <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                                    placeholder="Cabang / User">

                            </div>

                        </div>

                        <div class="mt-3">

                            <button class="btn btn-primary">

                                <i class="fas fa-search"></i>

                                Filter

                            </button>

                            <a href="{{ route('mutasi-stok.index') }}" class="btn btn-secondary">

                                Reset

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <table class="table mb-0 table-bordered table-hover">

                <thead class="table-light">

                    <tr>
                        <th width="60">No</th>
                        <th>Tanggal</th>
                        <th>Dari Cabang</th>
                        <th>Ke Cabang</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th width="120">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($transfers as $item)
                        <tr>

                            <td>
                                {{ $loop->iteration + ($transfers->firstItem() - 1) }}
                            </td>

                            <td>
                                {{ $item->transfer_date->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->fromBranch->name }}
                            </td>

                            <td>
                                {{ $item->toBranch->name }}
                            </td>

                            <td>

                                @switch($item->status)
                                    @case('draft')
                                        <span class="badge badge-secondary">
                                            Draft
                                        </span>
                                    @break

                                    @case('sent')
                                        <span class="badge badge-warning">
                                            Dikirim
                                        </span>
                                    @break

                                    @case('received')
                                        <span class="badge badge-success">
                                            Diterima
                                        </span>
                                    @break

                                    @case('cancelled')
                                        <span class="badge badge-danger">
                                            Batal
                                        </span>
                                    @break
                                @endswitch

                            </td>

                            <td>
                                {{ $item->user->name }}
                            </td>

                            <td>

                                <a href="{{ route('mutasi-stok.detail', $item->id) }}" class="btn btn-info btn-sm">

                                    <i class="fas fa-eye"></i>

                                </a>

                                <a href="{{ route('mutasi-stok.ubah', $item->id) }}" class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->id }}"
                                    data-url="{{ route('mutasi-stok.hapus', $item->id) }}">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </td>

                        </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
                                    Belum ada data
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="card-footer">
                {{ $transfers->links() }}
            </div>

        </div>

    @stop

    @section('js')

        <script>
            $(document).on('click', '.btn-delete', function() {

                let button = $(this);

                let url = button.data('url');

                Swal.fire({

                    title: 'Hapus Mutasi?',

                    html: `
            <div class="text-center">

                <i class="fas fa-exclamation-triangle text-warning"
                    style="font-size:55px;"></i>

                <p class="mt-3 mb-0">

                    Seluruh stok akan dikembalikan dan jurnal akan
                    direversal.

                </p>

                <strong class="text-danger">

                    Data yang sudah dihapus tidak dapat dikembalikan.

                </strong>

            </div>
        `,

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#d33',

                    cancelButtonColor: '#6c757d',

                    confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',

                    cancelButtonText: 'Batal',

                    reverseButtons: true,

                }).then((result) => {

                    if (!result.value) return;

                    $.ajax({

                        url: url,

                        type: "DELETE",

                        data: {

                            _token: "{{ csrf_token() }}"

                        },

                        beforeSend: function() {

                            Swal.fire({

                                title: 'Menghapus...',

                                text: 'Mohon tunggu.',

                                allowOutsideClick: false,

                                allowEscapeKey: false,

                                didOpen: () => {

                                    Swal.showLoading();

                                }

                            });

                        },

                        success: function(response) {
                            console.log(response);


                            Swal.fire({

                                icon: 'success',

                                title: 'Berhasil',

                                text: response.message,

                                timer: 1500,

                                showConfirmButton: false

                            }).then(() => {

                                location.reload();

                            });

                        },

                        error: function(xhr) {

                            Swal.fire({

                                icon: 'error',

                                title: 'Gagal',

                                text: xhr.responseJSON?.message ??
                                    'Terjadi kesalahan.'

                            });

                        }

                    });

                });

            });
        </script>

    @stop
