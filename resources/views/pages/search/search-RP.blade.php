<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Kode</th>
            <th scope="col">Jam Masuk</th>
            <th scope="col">Jam Keluar</th>
            <th scope="col">Pembongkar</th>
            <th scope="col">Pengirim</th>
            <th scope="col">Penerima</th>
            <th scope="col">Nopol</th>
            <th scope="col">Soper</th>
            @if (Route::currentRouteName() == 'search')
                <th scope="col">Nomer Sill</th>
                <th scope="col">Nomer Container</th>
                <th scope="col">Status</th>
            @endif
            {{-- <th scope="col">Pembuat</th>
                        <th scope="col">Pengedit</th> --}}
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if (count($data))
            @foreach ($data as $road_permit)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($road_permit->date)) }}</td>
                    <td>{{ $road_permit->code }}</td>
                    <td>{{ $road_permit->in }}</td>
                    <td>{{ $road_permit->out == null ? '' : date('d-m-Y H:i:s', strtotime($road_permit->out)) }}
                    </td>
                    <td>{{ $road_permit->handyman != null ? $road_permit->handyman->alias_name : '' }}</td>
                    <td>{{ $road_permit->from }}</td>
                    <td>{{ $road_permit->destination }}</td>
                    <td>{{ $road_permit->nopol }}</td>
                    <td>{{ $road_permit->driver }}</td>
                    @if (Route::currentRouteName() == 'search')
                        <td>{{ $road_permit->sill_number }}</td>
                        <td>{{ $road_permit->container_number }}</td>
                        <td>
                            <span
                                class="badge {{ $road_permit->status == 'Selesai' ? 'badge-success' : ($road_permit->status == 'Proses Bongkar' ? 'badge-warning' : 'badge-primary') }}">{{ $road_permit->status }}
                            </span>
                        </td>
                    @endif
                    {{-- <td>{{ $road_permit->createdBy != null ? $road_permit->createdBy->username : '' }}</td>
                                <td>{{ $road_permit->editedBy != null ? $road_permit->editedBy->username : '' }}</td> --}}
                    <td>
                        @if (Route::currentRouteName() == 'search')
                            @if ($road_permit->out == null)
                                <a href="{{ route('surat-jalan.keluar', $road_permit->id) }}"
                                    class="badge badge-warning"><i class="fas fa-hourglass-end"></i></a>
                                {{-- edit data --}}
                                {{-- <a href="{{ route('surat-jalan.set-pembongkar', ['type' => $type, 'id' => $road_permit->id]) }}"
                                    class="badge badge-primary"><i class="fas fa-male"></i></a> --}}
                            @endif
                            @if ($road_permit->out != null && $road_permit->handyman_id != null)
                                <a href="#" data-id="{{ $road_permit->id }}" class="badge badge-primary print"
                                    id="print"><i class="fas fa-print"></i>Print</a>
                            @endif
                        @endif
                        @can(['surat-jalan.detail', 'surat-jalan.LPBToSupplier'])
                            <a href="#" data-toggle="modal" data-id="{{ $road_permit->id }}" data-target="#details"
                                class="badge badge-primary show-detail"><i class="fas fa-eye"></i></a>
                            <a href="#" data-toggle="modal" data-id="{{ $road_permit->id }}" data-target="#details"
                                id="supplier" class="badge badge-success supplier"><i class="fas fa-eye"></i></a>
                        @endcan
                        <a href="{{ route('surat-jalan.ubah', ['type' => $type, 'id' => $road_permit->id]) }}"
                            class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
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

<style>
    h4,
    h5 {
        margin-top: 1rem;
        font-weight: bold;
    }

    table th,
    table td {
        text-align: center;
        vertical-align: middle;
    }

    table th {
        background-color: #f8f9fa;
    }

    .table-summary td {
        font-weight: bold;
        background-color: #f1f1f1;
    }

    .section {
        margin-bottom: 2rem;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="details" tabindex="-1" aria-labelledby="importKaryawan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Surat Jalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detail-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="printDetail()"><i
                        class="fas fa-print"></i>Cetak</button>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('.show-detail').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            let url = "{{ route('surat-jalan.detail', ':id') }}";
            url = url.replace(":id", id);

            $('#modal-detail-content').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
            );

            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    $('#modal-detail-content').html(res);
                    $('#modalDetail').modal('show');
                },
                error: function(xhr) {
                    $('#modal-detail-content').html(
                        '<div class="alert alert-danger">Gagal memuat data</div>');
                }
            });
        });
    });
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
<script>
    $(document).ready(function() {
        $('.supplier').click(function() {
            const id = $(this).data('id');
            let url = "{{ route('surat-jalan.LPBToSupplier', ':id') }}";
            url = url.replace(':id', id);
            $('#modal-detail-content').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
            );


            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {

                    $('#modal-detail-content').html(res);
                    $('#modalDetail').modal('show');
                },
                error: function(xhr) {
                    $('#modal-detail-content').html(
                        '<div class="alert alert-danger">Gagal memuat data</div>');
                }
            });
        });
    });
</script>

{{-- print ke rayyes --}}
<script>
    @section('plugins.PrintJs', true)
        $("#print").on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            let data = {
                id: id,
                model: "RoadPermit",
                relation: [
                    "createdBy",
                    "issuedBy",
                    "handyman",
                    "details"
                ]
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
                                                <strong>Jam Keluar: ${datas.out}</strong><br>
                                                <strong>Dibuat Oleh: ${datas.created_by != null ? datas.created_by.username : ""}</strong> 
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
                                                     ${datas.details && datas.details.length > 0 ? // Cek apakah datas.details ada dan tidak kosong
                                                        datas.details.map(detail => `
                                                        <td style="border: 2px solid black; padding: 8px; text-align: center;">${detail.amount} BTG</td>
                                                        `).join('') : // Jika tidak ada data
                                                        `<td style="border: 2px solid black; padding: 8px; text-align: center;" colspan="3">Tidak ada detail barang.</td>`
                                                    }
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
                                                <div style="text-align:center;">
                                                    Satpam, <br><br><br><br>
                                                    (${datas.issued_by != null ? datas.issued_by.username : ""})
                                                </div>
                                                <div style="text-align:center;">
                                                    Pengemudi, <br><br><br><br>
                                                    (${datas.driver})
                                                </div>
                                                <div style="text-align:center;">
                                                    Pembongkar, <br><br><br><br>
                                                    (${datas.handyman != null ? datas.handyman.fullname : ""})
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
