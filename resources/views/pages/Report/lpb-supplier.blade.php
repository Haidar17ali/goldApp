@extends('adminlte::page')

@section('title', 'Laporan LPB Supplier ')

@section('content_header')
    <h1>Laporan LPB Supplier</h1>
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
            <form id="filterForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <label for="dateBy"><small>Tanggal Berdasarkan</small></label>
                        <select class="form-control" name="date_by" id="dateBy">
                            <option value="">Pilih...</option>
                            <option value="paid_at">Pembayaran</option>
                            <option value="date">LPB</option>
                            <option value="arrival_date">Kedatangan</option>
                        </select>
                    </div>

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

                    <div class="col-md-2 d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="searchData">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
            <hr>

            {{-- hasil filter --}}
            <div class="d-flex justify-content-end mt-3">
                <a href="#" id="printLpbExcel" class="btn btn-success mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="#" id="printLpb" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            <div class="search-results"></div>
            <div class="search-pagination d-flex justify-content-end"></div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    {{-- <style>
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style> --}}
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


                        requestData.page = page;

                        // Update link export PDF
                        updateExportPdfLink();

                        $.ajax({
                                url: "{{ route('laporan.data-lpb-supplier') }}",
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

                function updateExportPdfLink() {
                    let start_date = $("#startDate").val();
                    let supplier = $("#supplier_id").val();
                    let nopol = $("#nopol").val();

                    let url = new URL("{{ route('laporan.lpb-supplier-export-pdf') }}", window.location.origin);
                    if (start_date) url.searchParams.append("start_date", start_date);
                    if (supplier && supplier !== "Silahkan Pilih Supplier") url.searchParams.append("supplier", supplier);
                    if (nopol) url.searchParams.append("nopol", nopol);

                    $('#printLpb').attr('href', url.toString());
                }

                function updateExportExcelLink() {
                    let date_by = $("#dateBy").val();
                    let start_date = $("#startDate").val();
                    let end_date = $("#lastDate").val();
                    let supplier = $("#supplier_id").val();
                    let nopol = $("#nopol").val();

                    let url = new URL("{{ route('laporan.all-lpb-supplier-export-excel') }}", window.location.origin);

                    if (date_by) url.searchParams.append("dateBy", date_by);
                    if (start_date) url.searchParams.append("start_date", start_date);
                    if (end_date) url.searchParams.append("last_date", end_date);
                    if (supplier) url.searchParams.append("supplier", supplier);
                    if (nopol) url.searchParams.append("nopol", nopol);

                    $('#printLpbExcel').attr('href', url.toString());
                }

                // Update link sebelum klik
                $('#printLpbExcel').on('click', function() {
                    updateExportExcelLink();
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
    @include('components.loading')
@stop
