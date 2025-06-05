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
            <div class="row justify-content-center g-2 align-items-end">
                <div class="col-md-2">
                    <label for="startDate"><small>Tanggal Awal</small></label>
                    <input type="date" class="form-control" id="startDate" name="start_date"
                        value="{{ old('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label for="lastDate"><small>Tanggal Akhir</small></label>
                    <input type="date" class="form-control" id="lastDate" name="last_date"
                        value="{{ old('last_date') }}">
                </div>

                <div class="col-md-2">
                    <label for="supplier_id"><small>Supplier</small></label>
                    <select class="form-control" name="supplier_id" id="supplier_id">
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="nopol"><small>Nopol</small></label>
                    <input type="text" class="form-control" id="nopol" name="nopol" value="{{ old('nopol') }}"
                        placeholder="Masukkan nopol">
                </div>

                <div class="col-md-2">
                    <label for="status"><small>Status</small></label>
                    <select class="form-control" name="status" id="status">
                        <option value="">Pilih Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex justify-content-end mt-3">
                    <button class="btn btn-primary search-data" id="searchData"><i class="fas fa-search"></i></button>
                </div>
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
                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                    });

                    function fetchData(start_date, last_date, supplier, nopol, page = 1, model, status) {
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
                        let supplier = $("#supplier_id").val();
                        let nopol = $("#nopol").val();
                        let status = $("#status").val();
                        fetchData(start_date, last_date, supplier, nopol, 1, "road_permits", status);

                        $('#searchData').on('click', function() {
                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier_id").val();
                            let nopol = $("#nopol").val();
                            let status = $("#status").val();
                            fetchData(start_date, last_date, supplier, nopol, 1, "road_permits", status);

                        });

                        $(document).on('click', '.pagination a', function(e) {
                            e.preventDefault();
                            let page = $(this).attr('href').split('page=')[1];



                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier_id").val();
                            let nopol = $("#nopol").val();
                            let status = $("#status").val();
                            fetchData(start_date, last_date, supplier, nopol, page, "road_permits", status);
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
                                th {
                                    font-weight: 900;
                                }

                                td {
                                    font-weight: 700;
                                }
                                .print-section {
                                    page-break-after: always;
                                    padding: 10px;
                                    font-family: Arial, sans-serif;
                                    font-size: 13px;
                                    color: #333;
                                }

                                .print-section:last-child {
                                    page-break-after: avoid;
                                }

                                .row {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-bottom: 2px;
                                }

                                .col-md-6 {
                                    width: 48%;
                                }

                                h6 {
                                    font-size: 14px;
                                    margin: 0 0 5px 0;
                                    font-weight: normal;
                                }

                                h6 strong {
                                    font-weight: bold;
                                }

                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 2px;
                                }

                                table,
                                th,
                                td {
                                    border: 1px solid #ccc;
                                }

                                th {
                                    background-color: #f2f2f2;
                                    font-weight: bold;
                                    padding: 4px;
                                    font-size: 12px;
                                }

                                td {
                                    padding: 4px;
                                    font-size: 12px;
                                    text-align: center;
                                }

                                .total-row {
                                    font-weight: bold;
                                    background-color: #e6f7ff;
                                }

                                /* .grand-total-table {
                                    margin-top: 5px;
                                } */

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
