@extends('adminlte::page')

@section('title', 'Pengelolaan Emas')

@section('content_header')
    <h1 class="mb-3">Daftar Pengelolaan Emas</h1>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0 float-left">Riwayat Pengelolaan</h4>
            <a href="{{ route('pengelolaan-emas.buat') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus-circle"></i> Tambah Pengelolaan
            </a>
        </div>

        <div class="card-body table-responsive p-5">
            <table class="table table-hover table-striped text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Produk</th>
                        <th>Karat</th>
                        <th>Gram Masuk (Customer)</th>
                        <th>Gram Hasil</th>
                        <th>Catatan</th>
                        <th>Dibuat Oleh</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($managements as $i => $m)
                        <tr>
                            <td>{{ $managements->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->date)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $badgeClass =
                                        [
                                            'sepuh' => 'warning',
                                            'patri' => 'info',
                                            'rosok' => 'secondary',
                                        ][$m->type] ?? 'light';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($m->type) }}</span>
                            </td>
                            <td>{{ $m->product->name ?? '-' }}</td>
                            <td>{{ $m->karat->name ?? '-' }}</td>
                            <td>{{ number_format($m->gram_in, 3) }} gr</td>
                            <td>{{ number_format($m->gram_out, 3) }} gr</td>
                            <td>{{ $m->note ?? '-' }}</td>
                            <td>{{ $m->creator->name ?? '-' }}</td>
                            <td>
                                {{-- <a href="{{ route('pengelolaan-emas.show', $m->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a> --}}
                                <a href="{{ route('pengelolaan-emas.ubah', $m->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-muted py-3">Belum ada data pengelolaan emas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-end">
                {{ $managements->links() }}
            </div>
        </div>
    </div>
@stop
