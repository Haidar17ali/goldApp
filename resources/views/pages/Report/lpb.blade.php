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
                <div class="col-md-1">
                    <select class="form-control" name="date_by" id="dateBy">
                        <option>Tanggal Berdasarkan...</option>
                        <option value="paid_at">Pembayaran</option>
                        <option value="date1">LPB</option>
                        <option value="date2">Kedatangan</option>
                        <option value="">Pemakaian</option>
                    </select>
                    <span class="text-danger error-text" id="nopol_error"></span>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="startDate" name="start_date"
                        value="{{ old('start_date') }}" placeholder="filter tanggal awal">
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
                <div class="col-md-1">
                    <input type="text" class="form-control" id="nopol" name="nopol" value="{{ old('nopol') }}"
                        placeholder="Nopol...">
                    <span class="text-danger error-text" id="nopol_error"></span>
                </div>
                <div class="col-md-1">
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
            <div class="d-flex justify-content-end mt-3">
                <button id="printLpb" class="btn btn-success" onclick="printLpb()">
                    <i class="fas fa-print"></i> Cetak LPB
                </button>
            </div>
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

                    function fetchData(page = 1) {
                        let dateBy = $("#dateBy").val();
                        let start_date = $("#startDate").val();
                        let last_date = $("#lastDate").val();
                        let supplier = $("#supplier_id").val();
                        let nopol = $("#nopol").val();
                        let status = $("#status").val();

                        let requestData = {};

                        if (dateBy && dateBy !== "Tanggal Berdasarkan...") {
                            requestData.dateBy = dateBy;
                        }

                        if (start_date) {
                            requestData.start_date = start_date;
                        }

                        if (last_date) {
                            requestData.last_date = last_date;
                        }

                        if (supplier && supplier !== "Silahkan Pilih Supplier") {
                            requestData.supplier = supplier;
                        }

                        if (nopol) {
                            requestData.nopol = nopol;
                        }

                        if (status && status !== "Pilih Status") {
                            requestData.status = status;
                        }

                        requestData.page = page;

                        $.ajax({
                                url: "{{ route('laporan.data-lpb') }}",
                                method: "GET",
                                data: requestData,
                                success: function(response) {
                                    if (response.status !== undefined) {
                                        @section('plugins.Toast', true)
                                            if (response.status == "no_start_date") {
                                                Toastify({
                                                    text: "Pencarian tanggal awal kosong!",
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

                        // First Load
                        fetchData(1);

                        // On Filter Search Button Click
                        $('#searchData').on('click', function() {
                            fetchData(1);
                        });

                        // Pagination Click
                        $(document).on('click', '.pagination a', function(e) {
                            e.preventDefault();
                            let page = $(this).attr('href').split('page=')[1];
                            fetchData(page);
                        });
                    });
    </script>
    <script>
        // Cetak LPB
        @section('plugins.PrintJs', true)
            function printLpb() {
                printJS({
                    printable: 'lpb-report-content',
                    type: 'html',
                    scanStyles: true, // penting untuk tetap ambil gaya Bootstrap dari halaman
                    style: `
                            @media print {
                                body {
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    font-family: 'Arial', sans-serif;
                                    font-size: 12px;
                                }

                                .btn,
                                .no-print,
                                .sidebar,
                                .navbar,
                                .footer {
                                    display: none !important;
                                }

                                .laporan-title {
                                    text-align: center;
                                    margin-bottom: 20px;
                                }

                                .laporan-title h2 {
                                    font-size: 20px;
                                    font-weight: bold;
                                }

                                .laporan-title p {
                                    font-size: 14px;
                                }

                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }

                                th, td {
                                    padding: 8px !important;
                                    text-align: center;
                                    border: 1px solid #dee2e6;
                                }

                                thead {
                                    background-color: #343a40 !important;
                                    color: white !important;
                                }

                                tfoot {
                                    font-weight: bold;
                                    background-color: #e9ecef !important;
                                }
                            }
                        `
                });
            }
    </script>

@stop
