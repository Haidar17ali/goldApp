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
                <button class="btn btn-success float-right" type="submit"><i class="fas fa-sync-alt"></i> Generate
                    Permission</button>
            </form>
        </div>
        <div class="card-body row">
            @if (count($permissions))
                @foreach ($permissions as $permission)
                    <div class="col-md-2 card">
                        <p class="text-left mx-auto"><i class="fas fa-dot-circle"></i> {{ $permission->name }}</p>
                    </div>
                @endforeach
            @else
                <div class="mx-auto">Data Permission Masih Belum Tersedia, Klik Generate Permission</div>
            @endif
        </div>
        <div class="card-footer">
            <ul>
                <li class="text-info">Untuk mengecek apakah ada permission baru silahkan klik generate permission</li>
            </ul>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        // toast
        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "added") {
                Toastify({
                    text: "Permission baru berhasil ditambahkan!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == "none") {
                Toastify({
                    text: "Tidak ada penambahan permission!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            }

            // delete
            // $(".badge-delete").click(function(e) {
            //     var form = $(this).closest("form");
            //     Swal.fire({
            //         title: 'Hapus Data!',
            //         text: "Apakah anda yakin akan menghapus data ini?",
            //         icon: 'warning',
            //         confirmButtonColor: '#3085d6',
            //         showCancelButton: true,
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Hapus!!'
            //     }).then((result) => {
            //         if (result.value === true) {
            //             form.closest("form").submit();
            //         }
            //     })
            // });
    </script>
@stop
