@extends('adminlte::page')

@section('title', 'Laporan Surat Jalan ')

@section('content_header')
    <h1>Laporan Surat Jalan</h1>
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
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-2">
                    <input type="date" class="form-control" id="startDate" name="start_date" value="{{ old('start_date') }}"
                        placeholder="filter tanggal awal">
                    <span class="text-danger error-text" id="start_date_error"></span>
                </div>
                <div class="col-md-1 text-center">
                    S.D
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="lastDate" name="last_date"
                        value="{{ old('last_date') }}" placeholder="filter tanggal awal">
                    <span class="text-danger error-text" id="last_date_error"></span>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="supplier_id" id="supplier_id">
                        <option>Silahkan Pilih Supplier</option>
                        @if (count($suppliers))
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="text-danger error-text" id="nopol_error"></span>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="nopol" name="nopol" value="{{ old('nopol') }}"
                        placeholder="Nopol...">
                    <span class="text-danger error-text" id="nopol_error"></span>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="status" id="status">
                        <option>Pilih Status</option>
                        @if (count($statuses))
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">
                                    {{ $status }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span class="text-danger error-text" id="nopol_error"></span>
                </div>
                <button class="btn btn-primary search-data" id="searchData"><i class="fas fa-search"></i></button>
            </div>
            <hr>

            {{-- hasil filter --}}
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
    <script src="{{ asset('assets/JS/myHelper.js') }}"></script>
    <script>
        $(document).ready(function() {
                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                    });

                    function fetchData(start_date, last_date, supplier, nopol, status, page = 1, model) {
                        let search = "";


                        $.ajax({
                                url: "{{ route('laporan.data-lpb') }}",
                                method: "GET",
                                data: {
                                    model: model,
                                    start_date: start_date,
                                    last_date: last_date,
                                    supplier: supplier,
                                    nopol: nopol,
                                    status: status,
                                    relations: {
                                        'createdBy': ["username"],
                                        'editedBy': ["username"],
                                        'handyman': ["fullname"],
                                    },
                                    columns: [
                                        'id',
                                        'code',
                                        'date',
                                        'in',
                                        'out',
                                        'handyman_id',
                                        'from',
                                        'destination',
                                        'nopol',
                                        'driver',
                                        'type',
                                        'created_by',
                                        'edited_by',

                                    ],
                                    page: page
                                },
                                success: function(response) {
                                    if (response.status != undefined) {
                                        @section('plugins.Toast', true)
                                            if (response.status == "no_start_date") {
                                                Toastify({
                                                    text: "Pencaian tanggal awal kosong!",
                                                    className: "danger",
                                                    close: true,
                                                    style: {
                                                        background: "red",
                                                    }
                                                }).showToast();
                                            }

                                        }

                                        $('.search-results').html(response.table);
                                        $('.search-pagination').html(response.pagination);
                                    }
                                });
                        }
                        let start_date = $("#startDate").val();
                        let last_date = $("#lastDate").val();
                        let supplier = $("#supplier").val();
                        let nopol = $("#nopol").val();
                        let status = $("#status").val();

                        fetchData(start_date, last_date, supplier, nopol, status, 1, "road_permits");

                        $('#searchData').on('click', function() {
                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier").val();
                            let nopol = $("#nopol").val();
                            let status = $("#status").val();
                            fetchData(start_date, last_date, supplier, nopol, status, 1, "road_permits");

                        });

                        $(document).on('click', '.pagination a', function(e) {
                            e.preventDefault();
                            let page = $(this).attr('href').split('page=')[1];



                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier").val();
                            let nopol = $("#nopol").val();
                            let status = $("#status").val();
                            fetchData(start_date, last_date, supplier, nopol, status, page, "road_permits");
                        });

                        // $('#searchBox').each(function() {
                        //     fetchData(this, 1, 'road_permits');
                        // });
                    })
    </script>

@stop
