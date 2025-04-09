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
                    <input type="text" class="form-control" id="nopol" name="nopol" value="{{ old('nopol') }}"
                        placeholder="Nopol...">
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

                    function fetchData(start_date, last_date, supplier, nopol, page = 1, model) {
                        let search = "";


                        $.ajax({
                                url: "{{ route('laporan.data-surat-jalan') }}",
                                method: "GET",
                                data: {
                                    model: model,
                                    start_date: start_date,
                                    last_date: last_date,
                                    supplier: supplier,
                                    nopol: nopol,
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
                        fetchData(start_date, last_date, supplier, nopol, 1, "road_permits");

                        $('#searchData').on('click', function() {
                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier").val();
                            let nopol = $("#nopol").val();
                            fetchData(start_date, last_date, supplier, nopol, 1, "road_permits");

                        });

                        $(document).on('click', '.pagination a', function(e) {
                            e.preventDefault();
                            let page = $(this).attr('href').split('page=')[1];



                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier").val();
                            let nopol = $("#nopol").val();
                            fetchData(start_date, last_date, supplier, nopol, page, "road_permits");
                        });

                        // $('#searchBox').each(function() {
                        //     fetchData(this, 1, 'road_permits');
                        // });
                    })
    </script>
    <script>
        function printDetail() {
            printJS({
                printable: 'modal-detail-content',
                type: 'html',
                style: `
            body {
                font-family: 'Arial', sans-serif;
                color: #333;
                font-size: 12px;
            }
            h4, h5 {
                margin: 10px 0;
                font-weight: bold;
                text-align: left;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
            }
            .section {
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th, td {
                border: 1px solid #555;
                padding: 6px;
                text-align: center;
                vertical-align: middle;
            }
            th {
                background-color: #f2f2f2;
            }
            .text-end {
                text-align: right;
            }
            .table-summary td {
                font-weight: bold;
                background-color: #fafafa;
            }
            .row {
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
                box-sizing: border-box;
                padding: 0 10px;
            }
            .mb-1 {
                margin-bottom: 6px;
            }
        `,
                scanStyles: false
            });
        }
    </script>
@stop
