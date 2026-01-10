@extends('adminlte::page')

@section('title', 'Varian Produk')

@section('content_header')
    <h1>Varian Produk</h1>
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
            <a href="{{ route('varian-produk.buat') }}" class="float-right btn btn-primary"><i class="fas fa-plus"></i>
                Varian Produk</a>

            <button type="button" class="float-right mr-1 btn btn-success" data-toggle="modal" data-target="#importModal"><i
                    class="fas fa-file-excel"></i>
                Import Excel</button>
            <div class="float-left">
                <input type="text" id="searchBox" data-model="productVariants" class="float-right mb-3 form-control"
                    placeholder="Cari Data...">
            </div>
        </div>
        <div class="card-body">
            {{-- test seach --}}
            <div class="search-results"></div>
            <div class="search-pagination d-flex justify-content-end"></div>
        </div>
    </div>

    {{-- modal for import --}}
    <!-- Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('varian-produk.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("post")
                    <div class="modal-body">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
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
        $(document).ready(function() {
            bsCustomFileInput.init()
        })
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
            } else if (status == "err-status") {
                Toastify({
                    text: "Status cutting yang sudah finish tidak bisa diedit!!",
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
                            'product_id',
                            'karat_id',
                            'gram',
                            'type',
                            'sku',
                            'barcode',
                            'default_price',
                        ],
                        relations: {
                            'product': ["name", "code"],
                            'karat': ["name"],
                        },
                        page: page
                    },
                    success: function(response) {

                        $('.search-results').html(response.table);
                        $('.search-pagination').html(response.pagination);
                    }
                });
            }
            fetchData("", 1, "productVariants");

            $('#searchBox').on('keyup', function() {
                fetchData(this, 1);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let inputElement = $("#searchBox");

                // Ambil model dari inputElement jika ada, jika tidak gunakan default model dari parameter
                let model = inputElement.length ? $(inputElement).data('model') : null;

                fetchData(inputElement, page, 'productVariants');
            });

            $('#searchBox').each(function() {
                fetchData(this, 1, 'productVariants');
            });
        });
    </script>
@stop
