@extends('adminlte::page')

@section('title', 'Pengeluaran')

@section('content_header')
    <h1>Data Pengeluaran</h1>
@stop

@section('content')

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <select name="branch_id" class="form-control">
                <option value="">-- Semua Cabang --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" 
                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <a href="{{route('pengeluaran-toko.buat')}}" class="btn btn-primary float-right"><i class="fas fa-plus"></i> Tambah Pengeluaran Toko</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->code }}</td>
                        <td>{{ $expense->date }}</td>
                        <td>{{ $expense->branch->name ?? '-' }}</td>
                        <td>Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{route('pengeluaran-toko.detail', $expense->id)}}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('pengeluaran-toko.edit', $expense->id) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('pengeluaran-toko.hapus', $expense->id) }}"
                                method="POST" class="d-inline form-delete">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>

                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-2">
            {{ $expenses->withQueryString()->links() }}
        </div>
    </div>
</div>

@stop

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🔥 KONFIRM DELETE
    $(document).on('submit', '.form-delete', function(e) {

        e.preventDefault()

        let form = this

        Swal.fire({
            title: 'Hapus pengeluaran?',
            text: "Data akan direverse di jurnal (tidak benar-benar hilang)",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {
                form.submit()
            }

        })
    })
</script>

<script>
    // ✅ SUCCESS
    @if (session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        })
    @endif

    // ❌ ERROR
    @if (session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000
        })
    @endif

    // ⚠️ WARNING (optional kalau mau)
    @if (session('warning'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: '{{ session('warning') }}',
            showConfirmButton: false,
            timer: 3000
        })
    @endif
</script>

@stop