@extends('adminlte::page')

@section('title', 'Detail Jurnal')

@section('content_header')
    <h1>Detail Jurnal</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Jurnal #{{ $journal->id }}
            </h3>

            <div class="card-tools">

                @if (!$journal->is_reversal && !$journal->reversedBy)
                    <form action="{{ route('jurnal.hapus', $journal->id) }}" method="POST"
                        onsubmit="return confirm('Reversal jurnal ini?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Reversal
                        </button>
                    </form>
                @endif

            </div>
        </div>

        <div class="card-body">

            {{-- HEADER INFO --}}
            <table class="table mb-4 table-bordered">

                <tr>
                    <th width="200">Tanggal</th>
                    <td>{{ $journal->date }}</td>
                </tr>

                <tr>
                    <th>Referensi</th>
                    <td>{{ $journal->reference ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $journal->description ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Sumber</th>
                    <td>
                        {{ $journal->source_type ?? '-' }}
                        {{ $journal->source_id ? '#' . $journal->source_id : '' }}
                    </td>
                </tr>

                @if ($journal->is_reversal)
                    <tr class="bg-warning">
                        <th>Status</th>
                        <td>
                            Jurnal Reversal dari #{{ $journal->reversal_of }}
                        </td>
                    </tr>
                @endif

            </table>

            {{-- DETAIL TABLE --}}
            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead class="thead-light">
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Kredit</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp

                        @foreach ($journal->items as $item)
                            @php
                                $totalDebit += $item->debit;
                                $totalCredit += $item->credit;
                            @endphp

                            <tr>
                                <td>{{ $item->account->code }}</td>
                                <td>{{ $item->account->name }}</td>

                                <td class="text-right">
                                    {{ number_format($item->debit, 2, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($item->credit, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                    <tfoot>

                        <tr class="bg-light font-weight-bold">
                            <td colspan="2" class="text-right">TOTAL</td>

                            <td class="text-right">
                                {{ number_format($totalDebit, 2, ',', '.') }}
                            </td>

                            <td class="text-right">
                                {{ number_format($totalCredit, 2, ',', '.') }}
                            </td>
                        </tr>

                    </tfoot>

                </table>

            </div>

            {{-- STATUS BALANCE --}}
            <div class="mt-3 text-right">

                @if ($totalDebit == $totalCredit)
                    <span class="p-2 badge badge-success">
                        Balance
                    </span>
                @else
                    <span class="p-2 badge badge-danger">
                        Tidak Balance
                    </span>
                @endif

            </div>

        </div>

    </div>

@stop
