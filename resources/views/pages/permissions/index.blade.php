@extends('adminlte::page')

@section('title', 'Permission')

@section('content_header')
    <h1>Permission</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('permission.generate') }}" method="POST">
                @csrf
                @method('post')
                <button class="btn btn-success float-right" type="submit">
                    <i class="fas fa-sync-alt"></i> Generate Permission
                </button>
            </form>
        </div>
        <div class="card-body">
            @if (count($permissions))
                @php
                    $grouped = collect($permissions)->groupBy(function ($permission) {
                        return explode('.', $permission->name)[0];
                    });
                @endphp

                @foreach ($grouped as $group => $items)
                    <h5 class="mb-3 mt-4"><i class="fas fa-folder-open text-primary"></i> {{ ucfirst($group) }}</h5>
                    <div class="row">
                        @foreach ($items as $permission)
                            <div class="col-md-3">
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body py-2 px-3">
                                        <i class="fas fa-dot-circle text-success mr-1"></i>
                                        <span>{{ $permission->name }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted">
                    <p>Data Permission masih belum tersedia.<br>Klik <strong>Generate Permission</strong> untuk memulai.</p>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <ul>
                <li class="text-info">
                    Untuk mengecek apakah ada permission baru, silakan klik tombol <strong>Generate Permission</strong>.
                </li>
            </ul>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
@section('plugins.Toast', true)
<script>
    var status = "{{ session('status') }}";
    if (status === "added") {
        Toastify({
            text: "Permission baru berhasil ditambahkan!",
            className: "info",
            close: true,
            style: {
                background: "#28A745"
            }
        }).showToast();
    } else if (status === "none") {
        Toastify({
            text: "Tidak ada penambahan permission!",
            className: "info",
            close: true,
            style: {
                background: "#17a2b8"
            }
        }).showToast();
    }
</script>
@stop
