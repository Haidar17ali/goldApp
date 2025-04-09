@extends('adminlte::page')

@section('title', 'Purchase Order ' . $type)

@section('content_header')
    <h1>Purchase Order {{ $type }}</h1>
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
            <a href="{{ route('purchase-order.buat', $type) }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i>
                Purchase Order {{ $type }}</a>
            <div class="float-left">
                <input type="text" id="searchBox" data-model="purchase_orders" class="form-control mb-3 float-right"
                    placeholder="Cari Data...">
            </div>
        </div>
        <div class="card-body">
            {{-- test seach --}}
            <div class="search-results"></div>
            <div class="search-pagination d-flex justify-content-end"></div>
            {{-- <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal PO</th>
                        <th scope="col">Kode PO</th>
                        <th scope="col">Supplier</th>
                        @if ($type == 'Sengon')
                            <th scope="col">Jenis Supplier</th>
                        @endif
                        <th scope="col">Status</th>
                        @if ($type != 'Sengon')
                            <th scope="col">PPN</th>
                            <th scope="col">DP</th>
                            <th scope="col">Pemesan</th>
                        @endif
                        <th scope="col">Pembuat</th>
                        <th scope="col">Pengedit</th>
                        <th scope="col">Penyetuju</th>
                        <th scope="col">Disetujui Tgl</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($pos))
                        @foreach ($pos as $po)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ date('d-m-Y', strtotime($po->po_date)) }}</td>
                                <td>{{ $po->po_code }}</td>
                                <td>{{ $po->supplier != null ? $po->supplier->name : '' }}</td>
                                @if ($type == 'Sengon')
                                    <td>{{ $po->supplier_type }}</td>
                                @endif
                                <td><span
                                        class="badge {{ $po->status == 'Aktif' ? 'badge-success' : ($po->status == 'Pending' ? 'badge-warning' : 'badge-danger') }}">{{ $po->status }}</span>
                                </td>
                                @if ($type != 'Sengon')
                                    <td>{{ $po->ppn }}</td>
                                    <td>{{ $po->dp }}</td>
                                    <td>{{ $po->order_by != null ? $po->order_by->fullname : '' }}</td>
                                @endif
                                <td>{{ $po->createdBy != null ? $po->createdBy->username : '' }}</td>
                                <td>{{ $po->edit_by != null ? $po->edit_by->username : '' }}</td>
                                <td>{{ $po->approvedBy != null ? $po->approvedBy->username : 'PO Belum Disetujui!' }}
                                <td>{{ $po->approved_at != null ? date('d-m-Y', strtotime($po->approved_at)) : '' }}
                                </td>
                                <td>

                                    @if ($po->approved_by != null && $po->status != 'Tidak Disetujui' && $po->status != 'Aktif' && $po->status != 'Gagal' && $po->status != 'Non-Aktif')
                                        @if (date('d-m-Y', strtotime($po->activation_date)) <= date('d-m-Y', strtotime(now()->toDateString())))
                                            <a href="{{ route('utility.activation-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Aktif']) }}"
                                                class="badge badge-success">Aktifkan</a>
                                            <a href="{{ route('utility.activation-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Gagal']) }}"
                                                class="badge badge-danger">Gagal</a>
                                        @else
                                            <span class="text-muted">Belum bisa diaktifkan</span>
                                        @endif
                                    @endif
                                    @if ($po->approved_by == null)
                                        <a href="{{ route('utility.approve-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Aktif']) }}"
                                            class="badge badge-success"><i class="fas fa-check"></i></a>
                                        <a href="{{ route('utility.approve-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Tidak Disetujui']) }}"
                                            class="badge badge-danger"><i class="fas fa-times"></i></a>
                                        ||
                                        <a href="{{ route('purchase-order.ubah', ['type' => $type, 'id' => $po->id]) }}"
                                            class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                                        <form action="{{ route('purchase-order.hapus', $po->id) }}" class="d-inline"
                                            id="delete{{ $po->id }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" data-id="{{ $po->id }}"
                                                class="badge badge-pill badge-delete badge-danger d-inline">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14" class="text-center"><b>Data tidak ditemukan!</b></td>
                        </tr>
                    @endif
                </tbody>
            </table> --}}
        </div>
    </div>


@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init()
        })
        let type = "{{ $type }}";
        localStorage.removeItem("edit" + type);
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
            } else if (status == "importSuccess") {
                Toastify({
                    text: "Data karyawan berhasil diimport!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            } else if (status == "Aktif") {
                Toastify({
                    text: "Purchase Order sudah Di setujui!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            } else if (status == "Non-Aktif") {
                Toastify({
                    text: "Purchase Order gagal Di setujui!",
                    className: "info",
                    close: true,
                    style: {
                        background: "#17a2b8",
                    }
                }).showToast();
            }

            // delete 
            $(".badge-delete").click(function(e) {
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
                    if (result.value === true) {
                        form.closest("form").submit();
                    }
                })
            });
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
                        type: "{{ $type }}",
                        columns: [
                            'id',
                            'po_date',
                            'po_type',
                            'supplier_id',
                            'created_by',
                            'edited_by',
                            'approved_by',
                            'po_code',
                            'status',
                            'po_type',

                        ],
                        relations: [
                            'supplier' ["name"],
                            'approvedBy' ["username"],
                        ],
                        page: page
                    },
                    success: function(response) {
                        console.log(response.pagination);

                        $('.search-results').html(response.table);
                        $('.search-pagination').html(response.pagination);
                    }
                });
            }
            fetchData("", 1, "purchase_orders");

            $('#searchBox').on('keyup', function() {
                fetchData(this, 1);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let inputElement = $("#searchBox");

                // Ambil model dari inputElement jika ada, jika tidak gunakan default model dari parameter
                let model = inputElement.length ? $(inputElement).data('model') : null;

                fetchData(inputElement, page, 'purchase_orders');
            });

            $('#searchBox').each(function() {
                fetchData(this, 1, 'purchase_orders');
            });
        });
    </script>
@stop
