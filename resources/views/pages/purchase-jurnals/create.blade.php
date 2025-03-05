@extends('adminlte::page')

@section('title', 'Buat Purchase-Jurnal ')

@section('content_header')
    <h1>Buat Purchase-Jurnal</h1>
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


            <div id="error-datas" style="color: red; margin-bottom: 10px;"></div>
            <div id="error-messages"></div>
            <div class="badge badge-primary float-right">Buat Purchase-Jurnal</div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Kode LPB</th>
                        <th>Supplier</th>
                        <th>Nopol</th>
                        <th>Afkir</th>
                        <th>130</th>
                        <th>260</th>
                        <th>Total Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($lpbs))
                        @foreach ($lpbs as $lpb)
                            <tr>
                                <td>{{ $lpb->code }}</td>
                                <td>{{ $lpb->supplier != null ? $lpb->supplier->name : '' }}</td>
                                <td>{{ $lpb->nopol }}</td>
                                <td>{{ $lpb->details != null ? $lpb->details->where('quality', 'Afkir')->where('length', '130')->sum('qty') : 0 }}
                                </td>
                                <td>{{ $lpb->details != null ? $lpb->details->where('quality', 'Super')->where('length', '130')->sum('qty') : 0 }}
                                </td>
                                <td>{{ $lpb->details != null ? $lpb->details->where('quality', 'Super')->where('length', '260')->sum('qty') : 0 }}
                                </td>
                                <td>Rp {{ money_format(nominalKubikasi($lpb->details)) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary mr-1" data-id="{{ $lpb->id }}"
                                        data-supplier="{{ $lpb->supplier != null ? $lpb->supplier->name : 'Supplier Tidak Ada' }}"
                                        data-code="{{ $lpb->code }}" data-kitir="{{ $lpb->no_kitir }}"
                                        data-nopol="{{ $lpb->nopol }}" data-details="{{ $lpb->details }}"
                                        data-vehicle="{{ $lpb->roadPermit != null ? $lpb->roadPermit->vehicle : 'Kendaraan Tidak Ada' }}"
                                        data-toggle="modal" data-target="#detailModal" id="detailLpb"><i
                                            class="fas fa-eye"></i></button>
                                    <a href="{{ route('lpb.ubah', $lpb->id) }}" class="btn btn-sm btn-success mr-1"><i
                                            class="fas fa-edit"></i></a>
                                    <button class="btn btn-sm btn-secondary"><i class="fas fa-check"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div id="loading" style="display: none;">
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <i class="fa fa-spinner fa-spin fa-3x"></i> <!-- Font Awesome spinner -->
            <p>Loading...</p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail LPB</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Kitir: <span id="modalKitir"></span></p>
                            <p>Kode LPB: <span id="modalKode"></span></p>
                            <p>Nama Supplier: <span id="modalSupplier"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p>Nopol: <span id="modalNopol"></span></p>
                            <p>Kendaraan: <span id="modalVehicle"></span></p>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produk Kode</th>
                                <th>Kualitas</th>
                                <th>Panjang</th>
                                <th>Diameter</th>
                                <th>Jumlah</th>
                                <th>Kubikasi</th>
                                <th>Harga/Kubikasi</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetail">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
    <style>
        /* Mengubah warna header */
        .ht_clone_top th {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
        }

        /* Mengatur border tabel */
        .htCore {
            border-collapse: collapse;
            border: 2px solid #ddd;
        }

        /* Mengubah warna sel saat dipilih */
        .ht_master .current {
            background-color: #a3a3a3 !important;
        }

        /* Mengatur padding dan font */
        .htCore td,
        .htCore th {
            padding: 10px;
            font-family: Arial, sans-serif;
            text-align: center;
        }

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
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>

    <script>
        localStorage.removeItem('editLpb');
        $(document).ready(function() {
                    // detail lpb
                    $("#detailLpb").on('click', function() {
                        let id = $(this).data('id');
                        let kitir = $(this).data('kitir');
                        let code = $(this).data('code');
                        let supplier = $(this).data('supplier');
                        let nopol = $(this).data('nopol');
                        let vehicle = $(this).data('vehicle');
                        let details = $(this).data('details');

                        // ganti text
                        $('#modalKitir').text(kitir);
                        $('#modalKode').text(code);
                        $('#modalSupplier').text(supplier);
                        $('#modalNopol').text(nopol);
                        $('#modalVehicle').text(vehicle);

                        var html = '';
                        $.each(details, function(index, detail) {
                            html += '<tr>';
                            html += '<td>' + detail.product_code + '</td>';
                            html += '<td>' + detail.quality + '</td>';
                            html += '<td>' + detail.length + '</td>';
                            html += '<td>' + detail.diameter + '</td>';
                            html += '<td>' + (detail.qty) + '</td>';
                            html += '<td>' + kubikasi(detail.diameter, detail.length, detail.qty) +
                                '</td>';
                            html += '<td>Rp' + money_format(detail.price, 0, ',', '.') + '</td>';
                            html += '<td>Rp' + money_format(kubikasi(detail.diameter, detail.length, detail
                                    .qty) *
                                detail.price, 0, ',', '.') + '</td>';
                            html += '</tr>';
                        });
                        $('#modalDetail').html(html);
                    })


                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                    });
                    // handsontable
                    let columnType = [{
                            data: 'diameter',
                            type: 'numeric'
                        },
                        {
                            data: 'afkir',
                            type: 'numeric'
                        },
                        {
                            data: '130',
                        },
                        {
                            data: '260',
                            type: 'numeric'
                        },
                        {
                            data: 'kubikasi_afkir',
                        },
                        {
                            data: 'kubikasi_130',
                        },
                        {
                            data: 'kubikasi_260',
                        },
                        {
                            data: 'total',
                        },
                    ];
                    let datas = [];
                    for (let i = 8; i <= 65; i++) {
                        datas.push({
                            diameter: i
                        });
                    }

                    $("#supplier_id").on('select2:select', function() {
                        let idAccount = $(this).val();
                        let url = "{{ route('utility.npwpId') }}"
                        let data = {
                            id: idAccount,
                            model: 'Supplier',
                            relation: ['npwp'],
                        }
                        let jsonData = loadWithData(url, data);

                        if (jsonData != null) {
                            $('#npwp').empty().append('<option value="">-- Pilih NPWP --</option>');

                            if (jsonData.npwp_id) {
                                $('#npwp').append('<option value="' + jsonData.npwp.id + '" selected>' + jsonData
                                    .npwp.name + '</option>');
                                $('#npwp_id').val(jsonData.npwp.id); // Set nilai ke input hidden
                            } else {
                                $('#npwp_id').val('');
                            }

                            $('#npwp').trigger('change'); // Refresh Select2
                        } else {
                            $('#npwp').empty().append('<option value="">-- Pilih NPWP --</option>');
                        }
                    });

                    $("#road_permit_id").on('change', function() {
                        let idAccount = $(this).val();
                        let url = "{{ route('utility.suratJalanId') }}"
                        let data = {
                            id: idAccount,
                            model: 'RoadPermit',
                        };
                        let jsonData = loadWithData(url, data);

                        if (jsonData != null) {
                            $("#nopol").val(jsonData.nopol);
                            $('#npwp_id').trigger('change'); // Refresh Select2
                        } else {
                            $("#nopol").val("");
                        }

                    });

                    const container = document.getElementById('handsontable-container');
                    let isUpdating = false; // Flag untuk mencegah rekursi
                    const hot = new Handsontable(container, {
                        minSpareRows: 1,
                        data: datas,
                        columns: columnType,
                        rowHeaders: true,
                        colHeaders: ['Diameter', 'Afkir', '130', '260', 'Kubikasi Afkir', 'Kubikasi 130',
                            'Kubikasi 260', 'Total'
                        ],
                        contextMenu: true,
                        autoColumnSize: true,
                        autoRowSize: true,
                        // manualRowResize: true,
                        // manualColumnResize: true,
                        persistentState: true,
                        licenseKey: 'non-commercial-and-evaluation',
                        stretchH: 'all',
                        afterChange: function(changes, source) {

                            if (source === 'edit' || source === 'paste') {
                                const data = hot.getData();
                                if (!isUpdating) {
                                    isUpdating = true; // Aktifkan flag sebelum update

                                    setTimeout(() => {
                                        hot.batch(() => {
                                            changes.forEach(([row, prop, oldVal,
                                                newVal
                                            ]) => {
                                                const diameter = hot
                                                    .getDataAtCell(row,
                                                        0) || 0;
                                                // kuantiti
                                                const qafkir = hot
                                                    .getDataAtCell(row,
                                                        1) || 0;
                                                const q130 = hot.getDataAtCell(
                                                    row,
                                                    2) || 0;
                                                const q260 = hot.getDataAtCell(
                                                    row,
                                                    3) || 0;



                                                hot.setDataAtCell(row, 4,
                                                    kubikasi(
                                                        diameter, 130,
                                                        qafkir));
                                                hot.setDataAtCell(row, 5,
                                                    kubikasi(
                                                        diameter, 130,
                                                        q130));
                                                hot.setDataAtCell(row, 6,
                                                    kubikasi(
                                                        diameter, 260,
                                                        q260));

                                                // set total kubikasi
                                                // kubikasi
                                                const kAfkir = hot
                                                    .getDataAtCell(row,
                                                        4) || 0;
                                                const k130 = hot.getDataAtCell(
                                                    row,
                                                    5) || 0;
                                                const k260 = hot.getDataAtCell(
                                                    row,
                                                    6) || 0;


                                                let totalKubikasi = parseFloat(
                                                        kAfkir) +
                                                    parseFloat(k130) +
                                                    parseFloat(k260);


                                                totalKubikasi = totalKubikasi
                                                    .toFixed(
                                                        4
                                                    ); // Batasi 4 angka di belakang koma

                                                hot.setDataAtCell(row, 7,
                                                    totalKubikasi);
                                                // end set Total kubikasi
                                            });
                                        });
                                        isUpdating = false; // Reset flag setelah update selesai

                                    }, 10);
                                }
                                localStorage.setItem('setLpb', JSON.stringify(data));
                            }
                        },
                    });

                    // Isi data dari localStorage saat halaman dimuat
                    function getLocalStorage() {
                        let columnHeadersType = ['diameter', 'afkir', '130', '260', 'kubikasi_afkir',
                            'kubikasi_130',
                            'kubikasi_260', 'total'
                        ];

                        const savedData = localStorage.getItem('setLpb');


                        if (savedData != null) {
                            try {
                                const parsedData = JSON.parse(savedData);

                                const columnHeaders = columnHeadersType; // Sesuaikan dengan jumlah kolom
                                const formattedData = parsedData.map(row => {
                                    let obj = {};
                                    row.forEach((value, index) => {
                                        obj[columnHeaders[index]] =
                                            value; // Konversi array ke objek dengan key yang benar
                                    });
                                    return obj;
                                });

                                hot.loadData(formattedData); // Memuat data ke Handsontable
                            } catch (error) {
                                console.error("Gagal memuat data dari localStorage:", error);
                            }
                        }
                    };


                    $('#formRP').on('submit', function(e) {
                            e.preventDefault();
                            document.getElementById('loading').style.display = 'flex';

                            let data = hot.getSourceData();
                            data = data.slice(0, -1);

                            if (data.length == 0) {
                                @section('plugins.Toast', true)
                                    Toastify({
                                        text: "Muatan tidak boleh kosong!",
                                        className: "danger",
                                        close: true,
                                        style: {
                                            background: "red",
                                        }
                                    }).showToast();
                                } //punya if


                                let permits = document.getElementById('details').value =
                                    JSON.stringify(data);

                                const form = e.target;
                                const formData = new FormData(form);



                                fetch(form.action, {
                                        method: form.method,
                                        body: formData,
                                        headers: {
                                            'Accept': 'application/json'
                                        },
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            return response.json().then(err => {
                                                throw err; // Lempar error agar bisa ditangkap di catch()
                                            });
                                        }
                                        return response.json();
                                        response.json()
                                    })
                                    .then(data => {

                                        if (data.errors) {
                                            $('.error-text').text('');
                                            $.each(data.errors, function(key, value) {
                                                if (key.startsWith('details')) {
                                                    $('#details_error').text(
                                                        'Terdapat kesalahan pada data muatan.');
                                                } else {
                                                    $('#' + key + '_error').text(value[0]);
                                                }
                                            });
                                        } else {
                                            localStorage.removeItem('setLpb');
                                            window.location.href = "{{ route('lpb.index') }}";
                                        }
                                    })
                                    .catch(error => {
                                        let errorContainer = document.getElementById("error-datas");

                                        // Pastikan elemen ada di DOM
                                        if (!errorContainer) {
                                            console.error("Elemen #error-datas tidak ditemukan di DOM.");
                                        } else {
                                            Object.entries(error.errors).forEach(([field, msgs]) => {
                                                let errorText = `${field}: ${msgs.join("| ")}<br>`;
                                                $("#error-datas").append(errorText)
                                            });
                                        }
                                    })
                                    .finally(() => {
                                        document.getElementById('loading').style.display = 'none';
                                    });


                            });

                        getLocalStorage();
                    });
    </script>
@stop
