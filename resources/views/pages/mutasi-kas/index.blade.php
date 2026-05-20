@extends('adminlte::page')

@section('title', 'Mutasi Kas')

@section('content_header')
    <h1>Mutasi Kas</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-header">
            <a href="{{ route('mutasi-kas.buat') }}" class="btn btn-primary btn-sm">
                Tambah Mutasi
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Referensi</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutations as $item)
                        <tr>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->reference }}</td>
                            <td>
                                {{ $item->fromBank->account_holder ?? 'Kas Tunai' }}
                            </td>
                            <td>
                                {{ $item->toBank->account_holder ?? 'Kas Tunai' }}
                            </td>
                            <td class="text-right">
                                {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td>{{ $item->note }}</td>
                            <td>
                                <a href="{{ route('mutasi-kas.edit', $item->id) }}" class="badge badge-success badge-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('mutasi-kas.hapus', $item->id) }}" method="POST"
                                    class="form-delete d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="border-0 badge badge-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $mutations->links() }}
            </div>
        </div>
    </div>

@stop

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('.btn-delete').click(function(e) {

            e.preventDefault();

            let form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus Data?',
                text: "Data mutasi kas akan dihapus",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {
                    form.submit();
                }

            });

        });
    </script>

@stop
