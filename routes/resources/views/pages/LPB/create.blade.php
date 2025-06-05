@extends('adminlte::page')

@section('title', 'Buat LPB ')

@section('content_header')
    <h1>Buat LPB</h1>
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
            <div class="badge badge-primary float-right">Buat LPB</div>
        </div>
    </div>
    <form action="{{ route('lpb.simpan') }}" method="POST" id="formRP">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="arrival_date" class="col-sm-2 col-form-label">Tanggal Kedatangan</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="arrival_date" name="arrival_date"
                                    value="{{ old('arrival_date') }}">
                                <span class="text-danger error-text" id="arrival_date_error"></span>
                            </div>
                            <label for="no_kitir" class="col-sm-2 col-form-label">No Kitir</label>
                            <div class="col-sm-4">
                                <input type="numeric" class="form-control" id="no_kitir" name="no_kitir"
                                    value="{{ old('no_kitir') }}">
                                <span class="text-danger error-text" id="no_kitir_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="grader_id" class="col-sm-2 col-form-label">Grader</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="grader_id" id="grader_id">
                                    <option value="">Silahkan Pilih Grader</option>
                                    @if (count($graderTallies))
                                        @foreach ($graderTallies as $graderTally)
                                            <option value="{{ $graderTally->id }}">{{ $graderTally->alias_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="tally_id" class="col-sm-2 col-form-label">Tally</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="tally_id" id="tally_id">
                                    <option value="">Silahkan Pilih Tally</option>
                                    @if (count($graderTallies))
                                        @foreach ($graderTallies as $graderTally)
                                            <option value="{{ $graderTally->id }}">{{ $graderTally->alias_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="road_permit_id" class="col-sm-2 col-form-label">Surat Jalan</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="road_permit_id" id="road_permit_id">
                                    <option>Silahkan Pilih Surat Jalan</option>
                                    @if (count($road_permits))
                                        @foreach ($road_permits as $road_permit)
                                            <option value="{{ $road_permit->id }}">
                                                {{ $road_permit->code . ' | ' . $road_permit->from . ' | ' . $road_permit->nopol . ' | ' . $road_permit->vehicle }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="status_sj" class="col-sm-1 col-form-label">Status SJ</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="status_sj" id="status_sj">
                                    <option>Pakai</option>
                                    <option>Selesai</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="supplier_id" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="supplier_id" id="supplier_id">
                                    <option>Silahkan Pilih Supplier</option>
                                    @if (count($suppliers))
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="npwp" class="col-sm-2 col-form-label">NPWP</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="npwp" id="npwp">
                                    <option>Silahkan Pilih NPWP</option>
                                    @if (count($npwps))
                                        @foreach ($npwps as $npwp)
                                            <option value="{{ $npwp->id }}">{{ $npwp->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" id="npwp_id" name="npwp_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nopol" class="col-sm-2 col-form-label">Nopol</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="nopol" name="nopol"
                                    value="{{ old('nopol') }}">
                                <span class="text-danger error-text" id="nopol_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="po_id" class="col-sm-2 col-form-label">Purchase Order</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="po_id" id="po_id">
                                    <option>Silahkan Pilih Purchase Order</option>
                                    @if (count($purchase_orders))
                                        @foreach ($purchase_orders as $purchase_order)
                                            <option value="{{ $purchase_order->id }}">
                                                {{ $purchase_order->po_code }}{{ $purchase_order->supplier == null ? ' | Umum' : ' | ' . $purchase_order->supplier->name }}
                                                {{ $purchase_order != null ? $purchase_order->description : '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="conversion" class="col-sm-2 col-form-label">Total Konversi</label>
                            <div class="col-sm-4">
                                <input type="numeric" class="form-control" id="conversion" name="conversion"
                                    value="{{ old('conversion') }}">
                                <span class="text-danger error-text" id="conversion_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="perhutani" class="col-sm-2 col-form-label">Perhutani</label>
                            <div class="toggle-container col-md-3">
                                <!-- Tombol Toggle -->
                                <label class="switch">
                                    <input type="checkbox" value="true" id="perhutani" name="perhutani">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <h3>Data Barang</h3>
                        <hr>
                        <span class="text-danger error-text" id="details_error"></span>
                        <div id="handsontable-container"></div>
                        <input type="hidden" name="details[]" id="details">

                        <div class="float-right mt-3">
                            <a href="{{ route('lpb.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="loading" style="display: none;">
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <i class="fa fa-spinner fa-spin fa-3x"></i> <!-- Font Awesome spinner -->
            <p>Loading...</p>
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

        /* toggle */

        /* Style untuk switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        /* Hide default checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Style untuk slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>

    <script>
        $(document).ready(function() {
                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                    });
                    $('#po_id').select2({
                        theme: "bootstrap4",
                    });
                    $('#road_permit_id').select2({
                        theme: "bootstrap4",
                    });
                    $('#grader_id').select2({
                        theme: "bootstrap4",
                    });
                    $('#tally_id').select2({
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
                    for (let i = 8; i <= 70; i++) {
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
                            relation: ["details"],
                        };
                        let jsonData = loadWithData(url, data);

                        // if (jsonData.details.length >= 0) {
                        //     $.each(jsonData.details, function(key, value) {
                        //         $('#cargo').append('<option value="' + value.id + '">' + value
                        //             .load + "||" + value.amount + ' Batang</option>');
                        //     });
                        // }

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
                        // rowHeaders: true,
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
    @include('components.loading')
@stop
