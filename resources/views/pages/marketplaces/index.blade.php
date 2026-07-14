@extends('adminlte::page')

@section('title', 'Marketplace')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>
            <i class="mr-2 fas fa-store"></i>
            Marketplace
        </h1>

        <a href="{{ route('marketplace.buat') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Marketplace
        </a>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible">

            <button class="close" data-dismiss="alert">&times;</button>

            <strong>Terdapat kesalahan :</strong>

            <ul class="mt-2 mb-0">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    <div class="card">

        <div class="card-header">

            <form method="GET">

                <div class="row">

                    <div class="col-md-4">

                        <div class="input-group">

                            <input type="text" name="search" class="form-control" placeholder="Cari Marketplace..."
                                value="{{ request('search') }}">

                            <div class="input-group-append">

                                <button class="btn btn-primary">

                                    <i class="fas fa-search"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

        <div class="p-0 card-body">

            <div class="table-responsive">

                <table class="table mb-0 table-hover table-bordered">

                    <thead class="thead-light">

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th width="70">
                                Logo
                            </th>

                            <th>
                                Marketplace
                            </th>

                            <th width="120">
                                Code
                            </th>

                            <th width="120">
                                Status
                            </th>

                            <th width="140">
                                Digunakan
                            </th>

                            <th width="80">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($marketplaces as $marketplace)
                            <tr>

                                <td>

                                    {{ $loop->iteration + ($marketplaces->currentPage() - 1) * $marketplaces->perPage() }}

                                </td>

                                <td class="text-center">

                                    @if ($marketplace->logo)
                                        <img src="{{ asset($marketplace->logo) }}" width="40" height="40"
                                            class="img-circle">
                                    @else
                                        <i class="fas fa-store fa-2x text-secondary"></i>
                                    @endif

                                </td>

                                <td>

                                    <strong>

                                        {{ $marketplace->name }}

                                    </strong>

                                </td>

                                <td>

                                    <span class="badge badge-info">

                                        {{ $marketplace->code }}

                                    </span>

                                </td>

                                <td>

                                    @if ($marketplace->is_active)
                                        <span class="badge badge-success">

                                            Aktif

                                        </span>
                                    @else
                                        <span class="badge badge-danger">

                                            Nonaktif

                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <span class="badge badge-primary">

                                        {{ $marketplace->transaction_marketplaces_count }}

                                    </span>

                                    Transaksi

                                </td>

                                <td>

                                    <div class="btn-group">

                                        <button class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">

                                            Aksi

                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right">

                                            <a href="{{ route('marketplace.ubah', $marketplace) }}" class="dropdown-item">

                                                <i class="mr-2 fas fa-edit"></i>

                                                Edit

                                            </a>

                                            <div class="dropdown-divider"></div>

                                            <form action="{{ route('marketplace.hapus', $marketplace) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus marketplace ini?')">

                                                @csrf

                                                @method('DELETE')

                                                <button class="dropdown-item text-danger">

                                                    <i class="mr-2 fas fa-trash"></i>

                                                    Hapus

                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="py-5 text-center text-muted">

                                    <i class="mb-3 fas fa-store fa-3x"></i>

                                    <br>

                                    Belum ada data marketplace.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($marketplaces->hasPages())
            <div class="card-footer">

                {{ $marketplaces->links() }}

            </div>
        @endif

    </div>

@stop

@section('css')

    <style>
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 13px;
        }

        .img-circle {
            object-fit: cover;
        }
    </style>

@stop

@section('js')

    <script>
        $(function() {

            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>

@stop
