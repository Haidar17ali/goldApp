@extends('adminlte::page')

@section('title', 'Laporan Detail DP Per Nopol')

@section('content')
    <div class="container card shadow-sm">
        <h4 class="mb-4 text-center pt-4">📊 Laporan Mutasi DP & LPB {{ $supplier->name }}</h4>

        {{-- Rekap Saldo --}}
        @if (count($rekap))
            <h3 class="mb-3">Rekap Saldo Nopol</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:60%">Nopol</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $r)
                            {{-- Baris Rekap --}}
                            <tr data-toggle="collapse" data-target="#collapse-{{ Str::slug($r['nopol'], '-') }}"
                                aria-expanded="false" aria-controls="collapse-{{ Str::slug($r['nopol'], '-') }}"
                                style="cursor:pointer;">
                                <td>
                                    <i class="fas fa-caret-right mr-1 text-primary"></i>
                                    <strong>{{ $r['nopol'] }}</strong>
                                </td>
                                <td class="text-right">
                                    <span class="p-2">
                                        Rp {{ number_format($r['saldo']) }}
                                    </span>
                                </td>
                            </tr>

                            {{-- Detail Collapse --}}
                            <tr class="collapse bg-light" id="collapse-{{ Str::slug($r['nopol'], '-') }}">
                                <td colspan="2">
                                    @if (isset($mutasiGrouped[$r['nopol']]))
                                        <div class="card card-body shadow-sm">
                                            <h5 class="mb-3">📌 Mutasi Nopol: <strong>{{ $r['nopol'] }}</strong></h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Jenis</th>
                                                            <th class="text-right">Masuk</th>
                                                            <th class="text-right">Keluar</th>
                                                            <th class="text-right">Saldo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($mutasiGrouped[$r['nopol']] as $mut)
                                                            <tr>
                                                                <td>{{ $mut['tanggal'] }}</td>
                                                                <td>{{ $mut['jenis'] }}</td>
                                                                <td class="text-success text-right">
                                                                    {{ number_format($mut['masuk']) }}
                                                                </td>
                                                                <td class="text-danger text-right">
                                                                    {{ number_format($mut['keluar']) }}
                                                                </td>
                                                                <td class="font-weight-bold text-right">
                                                                    {{ number_format($mut['saldo']) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        @php
                                                            $lastSaldo = end($mutasiGrouped[$r['nopol']])['saldo'] ?? 0;
                                                            reset($mutasiGrouped[$r['nopol']]);
                                                        @endphp
                                                        <tr class="bg-secondary text-white">
                                                            <th colspan="4" class="text-right">Saldo Akhir</th>
                                                            <th class="text-right">{{ number_format($lastSaldo) }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">Tidak ada data mutasi.</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-info text-white">
                        <tr>
                            <th class="text-right">Grand Total</th>
                            <th class="text-right">Rp {{ number_format($grandTotal) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Script toggle icon --}}
    <script>
        $(document).ready(function() {
            $('tr[data-toggle="collapse"]').on('click', function() {
                let icon = $(this).find('i.fas');
                icon.toggleClass('fa-caret-right fa-caret-down text-primary text-success');
            });
        });
    </script>
@endsection
