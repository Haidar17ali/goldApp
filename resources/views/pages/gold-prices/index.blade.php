@extends('adminlte::page')

@section('title', 'Harga Emas')

@section('content_header')
    <h1>Data Harga Emas (HPP)</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="float-right">
                <a href="{{ route('set-harga.buat') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Set Harga</a>
            </div>

            @forelse ($goldPrices as $date => $items)

                @php
                    $first = $items->first();
                @endphp

                <h5 class="mt-4">
                    {{ $first->description }}
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Kadar</th>
                                <th>Harga</th>
                                <th>Tanggal Aktif</th>
                                <th>Tanggal Expired</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($items as $hpp)
                                @foreach ($hpp->details as $detail)
                                    <tr>
                                        <td>{{ $detail->karat->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td>{{ $hpp->active_at }}</td>
                                        <td>{{ $hpp->expired_at ?? '-' }}</td>
                                        <td>
                                            @if ($hpp->status == 'Active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Expired</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                        </tbody>
                    </table>
                </div>

            @empty
                <div class="py-5 text-center">
                    <h5>Data harga emas masih kosong</h5>
                    <p class="text-muted">Silakan tambahkan data terlebih dahulu</p>
                </div>
            @endforelse

        </div>
    </div>

@stop
