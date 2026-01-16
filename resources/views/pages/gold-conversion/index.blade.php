@extends('adminlte::page')

@section('title', 'Input Etalase')

@section('content_header')
    <h1>Input Etalase</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Button trigger modal -->
            <a href="{{ route('konversi-emas.buat') }}" class="float-right btn btn-primary"><i class="fas fa-plus"></i>
                Etalase</a>
            <div class="float-left">
                <input type="text" id="searchBox" data-model="goldConversions" class="float-right mb-3 form-control"
                    placeholder="Cari Data...">
            </div>
        </div>
        <div class="card-body">
            {{-- test seach --}}
            <div class="search-results"></div>
            <div class="search-pagination d-flex justify-content-end"></div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script src="{{ asset('assets/JS/myHelper.js') }}"></script>
    <script>
        // toast
        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "saved") {
                Toastify({
                    text: "Data baru berhasil ditambahkan!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == 'edited') {
                Toastify({
                    text: "Data berhasil diubah!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == 'deleted') {
                Toastify({
                    text: "Data berhasil dihapus!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#28A745",
                    }
                }).showToast();
            } else if (status == "used") {
                Toastify({
                    text: "Data LPB Terpakai Dan Stock Berkurang!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            }
            $(document).ready(function() {
                // delete 
                $(document).on("click", ".badge-delete", function(e) {
                    e.preventDefault();
                    var form = $(this).closest("form");
                    Swal.fire({
                        title: 'Hapus Data!',
                        text: "Apakah anda yakin akan menghapus data ini?",
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        showCancelButton: true,
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Hapus!!'
                    }).then((result) => {
                        if (result.value) {
                            form.submit();
                        }
                    });
                });
            })
    </script>
    <script>
        $(document).ready(function() {
            function fetchData(inputElement = "", page = 1, model) {
                let search = "";

                // Jika inputElement valid, gunakan model dari elemen tersebut
                if (inputElement && $(inputElement).length > 0) {

                    model = $(inputElement).data('model');
                    search = $(inputElement).val();
                }


                $.ajax({
                    url: "{{ route('search') }}",
                    method: "GET",
                    data: {
                        model: model,
                        search: search,
                        columns: [
                            'id',
                            'stock_id',
                            'product_variant_id',
                            'input_weight',
                            'note',
                            'created_by',
                            'edited_by',
                            'created_at',
                        ],
                        relations: {
                            'productVariant.product': ["name"],
                            'productVariant.karat': ["name"],
                        },
                        page: page
                    },
                    success: function(response) {

                        $('.search-results').html(response.table);
                        $('.search-pagination').html(response.pagination);
                    }
                });
            }
            fetchData("", 1, "goldConversions");

            $('#searchBox').on('keyup', function() {
                fetchData(this, 1);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let inputElement = $("#searchBox");

                // Ambil model dari inputElement jika ada, jika tidak gunakan default model dari parameter
                let model = inputElement.length ? $(inputElement).data('model') : null;

                fetchData(inputElement, page, 'goldConversions');
            });

            $('#searchBox').each(function() {
                fetchData(this, 1, 'goldConversions');
            });
        });
    </script>
@stop
