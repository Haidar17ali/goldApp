@extends('adminlte::page')

@section('title', 'Laporan Detail DP ')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-center pt-4">Laporan Mutasi DP & LPB {{ $supplier->name }}</h4>

        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Nopol</th>
                    {{-- <th>Tanggal Kedatangan</th> --}}
                    {{-- <th>Keterangan</th> --}}
                    <th>Masuk (DP)</th>
                    <th>Keluar (LPB)</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasi as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                        <td class="text-center">{{ $item['jenis'] }}</td>
                        <td class="text-center">{!! $item['nopol'] !!}</td>
                        {{-- <td class="text-center">{{ $item['arrival_date'] }}</td> --}}
                        {{-- <td>{{ $item['keterangan'] }}</td> --}}
                        <td class="text-end text-success">
                            @if ($item['masuk'] > 0)
                                Rp {{ number_format($item['masuk'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-end text-danger">
                            @if ($item['keluar'] > 0)
                                Rp {{ number_format($item['keluar'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($item['saldo'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data mutasi.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="3"><strong>Grant Total</strong></td>
                    <td><strong>{{ money_format($saldoDP) }}</strong></td>
                    <td><strong>{{ money_format($saldoLpb) }}</strong></td>
                    <td><strong>{{ money_format($saldoDP - $saldoLpb) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
