@extends('adminlte::page')

@section('title', 'Surat Jalan')

@section('content_header')
    <h1>Surat Jalan{{ $type == 'In' ? ' Masuk' : ' Keluar' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if (session('import_errors'))
                <div class="alert alert-danger">
                    <h4>Kesalahan File Excel:</h4>
                    <ul>
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
            <a href="{{ route('surat-jalan.buat', $type) }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i>
                Surat Jalan {{ $type == 'In' ? ' Masuk' : ' Keluar' }}</a>
        </div>
        <div class="card-body row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jam Masuk</th>
                        <th scope="col">Jam Keluar</th>
                        <th scope="col">Pembongkar</th>
                        <th scope="col">Pengirim</th>
                        <th scope="col">Penerima</th>
                        <th scope="col">Nopol</th>
                        <th scope="col">Soper</th>
                        {{-- <th scope="col">Lok Bongkar</th> --}}
                        <th scope="col">Nomer Sill</th>
                        <th scope="col">Nomer Container</th>
                        <th scope="col">Status</th>
                        {{-- <th scope="col">Pembuat</th>
                        <th scope="col">Pengedit</th> --}}
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($road_permits))
                        @foreach ($road_permits as $road_permit)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ date('d-m-Y', strtotime($road_permit->date)) }}</td>
                                <td>{{ $road_permit->in }}</td>
                                <td>{{ $road_permit->out == null ? '' : date('d-m-Y H:i:s', strtotime($road_permit->out)) }}
                                </td>
                                <td>{{ $road_permit->handyman != null ? $road_permit->handyman->alias_name : '' }}</td>
                                <td>{{ $road_permit->from }}</td>
                                <td>{{ $road_permit->destination }}</td>
                                <td>{{ $road_permit->nopol }}</td>
                                <td>{{ $road_permit->driver }}</td>
                                {{-- <td>{{ $road_permit->unpack_location }}</td> --}}
                                <td>{{ $road_permit->sill_number }}</td>
                                <td>{{ $road_permit->container_number }}</td>
                                <td><span
                                        class="badge {{ $road_permit->status == 'Selesai' ? 'badge-success' : ($road_permit->status == 'Proses Bongkar' ? 'badge-warning' : 'badge-primary') }}">{{ $road_permit->status }}</span>
                                </td>
                                {{-- <td>{{ $road_permit->createdBy != null ? $road_permit->createdBy->username : '' }}</td>
                                <td>{{ $road_permit->editedBy != null ? $road_permit->editedBy->username : '' }}</td> --}}
                                <td>
                                    @if ($road_permit->out == null)
                                        <a href="{{ route('surat-jalan.keluar', $road_permit->id) }}"
                                            class="badge badge-warning"><i class="fas fa-hourglass-end"></i></a>
                                        {{-- edit data --}}
                                        <a href="#" data-id="{{ $road_permit->id }}"
                                            class="badge badge-primary print"><i class="fas fa-print"></i>Print</a>
                                        {{-- <a href="{{ route('surat-jalan.set-pembongkar', ['type' => $type, 'id' => $road_permit->id]) }}"
                                            class="badge badge-primary"><i class="fas fa-male"></i></a> --}}
                                        <a href="{{ route('surat-jalan.ubah', ['type' => $type, 'id' => $road_permit->id]) }}"
                                            class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                                    @endif
                                    <form action="{{ route('surat-jalan.hapus', $road_permit->id) }}" class="d-inline"
                                        id="delete{{ $road_permit->id }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" data-id="{{ $road_permit->id }}"
                                            class="badge badge-pill badge-delete badge-danger d-inline">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="13" class="text-center"><b>Data surat-jalan tidak ditemukan!</b></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal box untuk import karyawan --}}
    <!-- Modal -->
    <div class="modal fade" id="importSupplier" tabindex="-1" aria-labelledby="importKaryawan" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('supplier.import') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        @method('post')
                        <div class="row">
                            <label for="file" class="col-sm-2 col-form-label">Excel</label>
                            <div class="col-sm-10">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file" id="file">
                                    <label class="custom-file-label" for="file">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- print js --}}
    <div id="print-area"></div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script src="{{ asset('assets/JS/myHelper.js') }}"></script>
    <script>
        localStorage.removeItem('editRoadPermitDetails');
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
            } else if (status == "importSuccess") {
                Toastify({
                    text: "Data karyawan berhasil diimport!",
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
            @section('plugins.PrintJs', true)
                $(".print").on('click', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    let data = {
                        id: id,
                        model: "RoadPermit"
                    };

                    let datas = loadWithData("{{ route('utility.cetak-surat-jalan') }}", data)

                    // Template untuk print
                    let printContent = `
                                        <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px;">
                                            CV. JATI MAKMUR
                                        </div>

                                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px;">
                                            <div><strong>Kode Surat Jalan:</strong> ${datas.code}</div>
                                            <div><strong>Tanggal Kedatangan:</strong> ${datas.date}</div>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px;">
                                            <div>
                                                <strong>Pengirim:</strong> ${datas.from}<br>
                                                <strong>Tujuan:</strong> ${datas.destination}<br>
                                                <strong>Nopol:</strong> ${datas.nopol}<br>
                                                <strong>Kendaraan:</strong> ${datas.vehicle}
                                            </div>
                                            <div>
                                                <strong>Jam Masuk:</strong> ${datas.in}<br>
                                                <strong>Jam Keluar:</strong> 
                                            </div>
                                        </div>

                                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; border: 2px solid black;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 2px solid black; text-align:center; padding: 8px; background-color: #f2f2f2;">Afkir</th>
                                                    <th style="border: 2px solid black; text-align:center; padding: 8px; background-color: #f2f2f2;">130</th>
                                                    <th style="border: 2px solid black; text-align:center; padding: 8px; background-color: #f2f2f2;">260</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="border: 2px solid black; padding: 8px; text-align: center;">..... BTG</td>
                                                    <td style="border: 2px solid black; padding: 8px; text-align: center;">..... BTG</td>
                                                    <td style="border: 2px solid black; padding: 8px; text-align: center;">..... BTG</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="margin-top: 20px; font-size: 12px;">
                                            <strong>Keterangan:</strong><br>
                                            1. Putih Untuk CV. Jati Makmur<br>
                                            2. Merah Untuk Pengemudi<br>
                                            3. Kuning Untuk Satpam
                                        </div>

                                        <div style="margin-top: 30px; font-size: 14px;">
                                            <div style="display: flex; justify-content: space-around;">
                                                <div>
                                                    Satpam, <br><br><br><br>
                                                    (............................)
                                                </div>
                                                <div>
                                                    Pengemudi, <br><br><br><br>
                                                    (............................)
                                                </div>
                                                <div>
                                                    Pembongkar, <br><br><br><br>
                                                    (............................)
                                                </div>
                                            </div>
                                        </div>
                                    `;

                    // Tampilkan di area cetak
                    $("#print-area").html(printContent);

                    // Jalankan Print.js
                    printJS({
                        printable: 'print-area',
                        type: 'html',
                        style: `
                                    body { font-family: Arial, sans-serif; font-size: 14px; }
                                    table { width: 100%; border-collapse: collapse; }
                                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                                    th { background-color: #f2f2f2; }
                                    div { margin-bottom: 10px; }
                                `,
                        scanStyles: false
                    });


                });
    </script>
@stop
