@extends('adminlte::page')

@section('title', 'Edit Payroll')

@section('content_header')
    <h1>Edit Gaji</h1>
@stop

@section('content')

    <form action="{{ route('payroll.update', [$year, $month]) }}" method="POST">
        @csrf
        @method('patch')

        <div class="card">

            {{-- Header --}}
            <div class="card-header">

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                <div class="row">

                    <div class="col-md-3">
                        <label>Bulan</label>
                        <input type="number" id="month" name="month" value="{{ $month }}" class="form-control"
                            readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Tahun</label>
                        <input type="number" id="year" name="year" value="{{ $year }}" class="form-control"
                            readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Hari Kerja</label>
                        <input type="text" id="hariKerja" value="{{ $daysInMonth }}" class="form-control" readonly>
                    </div>

                </div>

            </div>

            {{-- Body --}}
            <div class="card-body table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Gaji Pokok</th>
                            <th>Hari Kerja</th>
                            <th>Bonus</th>
                            <th>Potongan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payrolls as $i => $p)
                            <tr>

                                <td>{{ $p->user->id }}</td>
                                <td>{{ $p->user->profile?->nama ?? $p->user->username }}</td>

                                <td>
                                    <input type="hidden" name="data[{{ $i }}][user_id]"
                                        value="{{ $p->user_id }}">

                                    <input type="number" name="data[{{ $i }}][gaji]"
                                        value="{{ $p->gaji }}" class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][hari_kerja]"
                                        value="{{ $p->hari_kerja }}" class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][bonus]"
                                        value="{{ $p->bonus }}" class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][potongan]"
                                        value="{{ $p->potongan }}" class="form-control">
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

            {{-- Footer --}}
            <div class="text-right card-footer">
                <button type="submit" class="btn btn-primary">
                    Update Payroll
                </button>
            </div>

        </div>

    </form>

@stop

@section('js')
    <script>
        function updateHariKerja() {

            let month = document.getElementById('month').value;
            let year = document.getElementById('year').value;

            if (month && year) {

                let days = new Date(year, month, 0).getDate();

                document.getElementById('hariKerja').value = days;

                document.querySelectorAll('input[name$="[hari_kerja]"]')
                    .forEach(input => input.value = days);
            }
        }

        document.getElementById('month').addEventListener('input', updateHariKerja);
        document.getElementById('year').addEventListener('input', updateHariKerja);
    </script>
@stop
