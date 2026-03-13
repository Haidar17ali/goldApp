@extends('adminlte::page')

@section('title', 'Generate Payroll')

@section('content_header')
    <h1>Generate Gaji</h1>
@stop

@section('content')

    <form action="{{ route('payroll.simpan') }}" method="POST">
        @csrf

        <div class="card">

            {{-- Header --}}
            <div class="card-header">

                <div class="row">

                    <div class="col-md-3">
                        <label>Bulan</label>
                        <input type="number" id="month" name="month" value="{{ $month }}" class="form-control"
                            required>
                    </div>

                    <div class="col-md-3">
                        <label>Tahun</label>
                        <input type="number" id="year" name="year" value="{{ $year }}" class="form-control"
                            required>
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
                        @foreach ($users as $i => $user)
                            <tr>

                                <td>{{ $user->id }}</td>
                                <td>{{ $user->profile?->nama ?? $user->username }}</td>

                                <td>
                                    <input type="hidden" name="data[{{ $i }}][user_id]"
                                        value="{{ $user->id }}">

                                    <input type="number" name="data[{{ $i }}][gaji]"
                                        value="{{ $user->profile?->gaji ?? 0 }}" class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][hari_kerja]"
                                        value="{{ $daysInMonth }}" class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][bonus]" value="0"
                                        class="form-control">
                                </td>

                                <td>
                                    <input type="number" name="data[{{ $i }}][potongan]" value="0"
                                        class="form-control">
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

            {{-- Footer --}}
            <div class="text-right card-footer">
                <button type="submit" class="btn btn-success">
                    Simpan Payroll
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

                // JS month index mulai dari 0
                let days = new Date(year, month, 0).getDate();

                document.getElementById('hariKerja').value = days;

                // update semua field hari_kerja per user
                document.querySelectorAll('input[name$="[hari_kerja]"]')
                    .forEach(input => input.value = days);
            }
        }

        // Trigger saat berubah
        document.getElementById('month').addEventListener('input', updateHariKerja);
        document.getElementById('year').addEventListener('input', updateHariKerja);
    </script>
@stop
