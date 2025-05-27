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

            <button onclick="printLPBNopol()" class="btn btn-success no-print float-right"><i
                    class="fas fa-print"></i>Cetak</button>
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
        @section('plugins.PrintJs', true)
            function printLPBNopol() {
                printJS({
                    printable: "print-preview",
                    type: 'html',
                    scanStyles: true,
                    style: `
                            @media print {
                                .print-section {
                                    page-break-after: always;
                                    padding: 20px;
                                    font-family: Arial, sans-serif;
                                    font-size: 13px;
                                    color: #333;
                                }

                                .print-section:last-child {
                                    page-break-after: avoid;
                                }

                                .lpb-header {
                                    margin-bottom: 10px;
                                    padding-bottom: 5px;
                                    border-bottom: 2px solid #666;
                                }

                                .lpb-header h4 {
                                    margin: 0;
                                    font-size: 16px;
                                    color: #000;
                                }

                                .lpb-header h5 {
                                    margin: 5px 0 0 0;
                                    font-size: 14px;
                                    color: #555;
                                }

                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 15px;
                                }

                                table,
                                th,
                                td {
                                    border: 1px solid #ccc;
                                }

                                th {
                                    background-color: #f2f2f2;
                                    font-weight: bold;
                                    padding: 8px;
                                    text-align: center;
                                }

                                td {
                                    padding: 6px;
                                    text-align: center;
                                }

                                .total-row {
                                    font-weight: bold;
                                    background-color: #e6f7ff;
                                }

                                .grand-total-table {
                                    margin-top: 20px;
                                }

                                .grand-total-table th {
                                    background-color: #d9ead3;
                                    color: #000;
                                }

                                .grand-total-table td {
                                    background-color: #f6fff2;
                                }
                            }
                        `
                });
            }
    </script>
    @include('components.loading')
@stop
