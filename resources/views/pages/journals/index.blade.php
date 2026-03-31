@extends('adminlte::page')

@section('title', 'Jurnal Umum')

@section('content_header')
    <h1>Jurnal Umum</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Daftar Jurnal</h3>

            <div class="card-tools">
                <a href="{{ route('jurnal.buat') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Buat Jurnal
                </a>
            </div>
        </div>

        <div class="card-body">

            {{-- 🔎 FILTER --}}
            <form method="GET" action="{{ route('jurnal.index') }}" class="mb-4">

                <div class="row">

                    <div class="col-md-3">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label>Cari Jurnal</label>
                        <input type="text" name="search" class="form-control" placeholder="Reference / Deskripsi"
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="mr-2 btn btn-primary w-50">
                            <i class="fas fa-search"></i> Filter
                        </button>

                        <a href="{{ route('jurnal.index') }}" class="btn btn-secondary w-50">
                            Reset
                        </a>
                    </div>

                </div>

            </form>


            {{-- 📊 TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th width="120">Tanggal</th>
                            <th width="160">Referensi</th>
                            <th>Deskripsi</th>
                            <th width="150" class="text-right">Debit</th>
                            <th width="150" class="text-right">Kredit</th>
                            <th width="120">Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($journals as $journal)
                            @php
                                $debit = $journal->items->sum('debit');
                                $credit = $journal->items->sum('credit');
                            @endphp

                            <tr>

                                <td>{{ \Carbon\Carbon::parse($journal->date)->format('d-m-Y') }}</td>

                                <td>
                                    <strong>{{ $journal->reference }}</strong>
                                </td>

                                <td>{{ $journal->description }}</td>

                                <td class="text-right text-success">
                                    {{ number_format($debit, 0, ',', '.') }}
                                </td>

                                <td class="text-right text-danger">
                                    {{ number_format($credit, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($debit == $credit)
                                        <span class="badge badge-success">
                                            Balance
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            Tidak Balance
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    <a href="{{ route('jurnal.detail', $journal->id) }}"
                                        class="badge badge-info badge-pill badge-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($journal->reversedBy == null)
                                        @if ($journal->original == null)
                                            <a href="{{ route('jurnal.edit', $journal->id) }}"
                                                class="badge badge-pill badge-warning badge-xs">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('jurnal.hapus', $journal->id) }}" class="d-inline"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" data-id="{{ $journal->id }}"
                                                    class="badge badge-pill badge-delete badge-danger d-inline">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </form>
                                        @endif
                                    @endif
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center">
                                    Tidak ada data jurnal
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- 📄 PAGINATION --}}
            <div class="mt-3">
                {{ $journals->links() }}
            </div>

        </div>

    </div>

@stop



@section('plugins.Toast', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script src="{{ asset('assets/JS/myHelper.js') }}"></script>
    <script>
        // toast
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
