@extends('adminlte::page')

@section('title', 'Payroll')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Payroll Bulanan</h1>

        <a href="{{ route('payroll.generate') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate Gaji Bulan Ini
        </a>
    </div>
@stop

@section('content')

    {{-- ALERT --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rekap Payroll per Bulan</h3>
        </div>

        <div class="p-0 card-body table-responsive">

            <table class="table table-hover text-nowrap">
                <thead class="thead-light">
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Karyawan</th>
                        <th>Total Gaji</th>
                        <th>Total Bonus</th>
                        <th>Total Potongan</th>
                        <th>Total Dibayarkan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($months as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->bulan_nama }}</strong>
                            </td>

                            <td>{{ $m->jumlah_karyawan }}</td>

                            <td>Rp {{ number_format($m->total_gaji, 0, ',', '.') }}</td>

                            <td>Rp {{ number_format($m->total_bonus, 0, ',', '.') }}</td>

                            <td>Rp {{ number_format($m->total_potongan, 0, ',', '.') }}</td>

                            <td>
                                <strong>
                                    Rp
                                    {{ number_format($m->total_gaji + $m->total_bonus - $m->total_potongan, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                <a href="{{ route('payroll.detail', ['year' => $m->tahun, 'month' => $m->bulan]) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('payroll.edit', ['year' => $m->tahun, 'month' => $m->bulan]) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('payroll.hapus', ['year' => $m->tahun, 'month' => $m->bulan]) }}"
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
                            <td colspan="7" class="text-center text-muted">
                                Belum ada data payroll
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

@stop


@section('Sweetalert2')
@section('js')
    <script>
        $(document).on('submit', '.form-delete', function(e) {

            e.preventDefault()

            let form = this

            Swal.fire({
                title: 'Hapus payroll?',
                text: "Semua data payroll bulan ini akan dihapus & jurnal akan direverse!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.value) {
                    console.log('confirm');
                    form.submit()
                }


            })

        })
    </script>
    <script>
        @if (session('status'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('status') }}',
                showConfirmButton: false,
                timer: 3000
            })
        @endif

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
    </script>
@stop
