@extends('adminlte::page')

@section('title', 'Laporan Penerimaan Barang ')

@section('content_header')
    <h1>Laporan Penerimaan Barang</h1>
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
                <div class="row g-2 align-items-end">

                    <div class="col-md-2">
                        <label for="dateBy"><small>Tanggal Berdasarkan</small></label>
                        <select class="form-control" name="dateBy" id="dateBy">
                            <option value="">Pilih...</option>
                            <option value="paid_at">Pembayaran</option>
                            <option value="date1">LPB</option>
                            <option value="date2">Kedatangan</option>
                            <option value="pemakaian">Pemakaian</option>
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

                    <div class="col-md-2">
                        <label for="status"><small>Status</small></label>
                        <select class="form-control" name="status" id="status">
                            <option value="">Pilih Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="searchData">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="d-flex justify-content-end mt-3">
                <button id="printLpb" class="btn btn-success" onclick="printLpb()">
                    <i class="fas fa-print"></i> Cetak LPB
                </button>
                @can('laporan.export-Lpb-Npwp')
                    <a href="#" id="exportExcel" class="btn btn-success ml-1"><i class="fas fa-file-excel"></i> Export
                        NPWP</a>
                @endcan
            </div>

            <div class="search-results"></div>
            <div class="search-pagination d-flex justify-content-end"></div>

        </div>

    </div>




@stop

@section('css')
    <style>
        .compact-table {
            font-size: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .compact-table th,
        .compact-table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }

        .compact-table th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="{{ asset('assets/JS/myHelper.js') }}"></script>
    <script>
        $(document).ready(function() {
                    $('#supplier_id').select2({
                        theme: "bootstrap-5",
                    }).on('select2:open', function() {
                        // Fokuskan ke input search
                        setTimeout(() => {
                            document.querySelector('.select2-container--open .select2-search__field')
                                ?.focus();
                        }, 10);
                    });;

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
                        $('#exportExcel').on('click', function(e) {
                            e.preventDefault();

                            let dateBy = $("#dateBy").val();
                            let start_date = $("#startDate").val();
                            let last_date = $("#lastDate").val();
                            let supplier = $("#supplier_id").val();
                            let nopol = $("#nopol").val();
                            let status = $("#status").val();

                            let url = new URL("{{ route('laporan.export-Lpb-Npwp') }}", window.location.origin);

                            if (dateBy) url.searchParams.append('dateBy', dateBy);
                            if (start_date) url.searchParams.append('start_date', start_date);
                            if (last_date) url.searchParams.append('last_date', last_date);
                            if (supplier) url.searchParams.append('supplier', supplier);
                            if (nopol) url.searchParams.append('nopol', nopol);
                            if (status) url.searchParams.append('status', status);

                            window.location.href = url.toString(); // Redirect untuk download file
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
                                    display: table-row-group;
                                }
                            }
                        `
                });
            }
    </script>
    @include('components.loading')

@stop
