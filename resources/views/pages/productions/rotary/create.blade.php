@extends('adminlte::page')

@section('title', 'Input Hasil', $type)

@section('content_header')
    <h1>Input Hasil {{ $type }}</h1>
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
            <div class="badge badge-primary float-right">Input Hasil {{ $type }}</div>
        </div>
    </div>
    <form action="{{ route('rotari.simpan', $type) }}" method="POST" id="formRP">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="arrival_date" class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ old('date') }}">
                                <span class="text-danger error-text" id="date_error"></span>
                            </div>
                            <label for="shift" class="col-sm-2 col-form-label">Shift</label>
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="shift" id="shift1"
                                        value="1">
                                    <label class="form-check-label" for="shift1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="shift" id="shift2"
                                        value="2">
                                    <label class="form-check-label" for="shift2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="shift" id="shift3"
                                        value="3">
                                    <label class="form-check-label" for="shift3">3</label>
                                </div>
                                <span class="text-danger error-text" id="shift_error"></span>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="no_lpb" class="col-sm-2 col-form-label">No Kitir</label>
                            <div class="col-sm-4">
                                <select class="form-control" multiple name="kitirs[]" id="no_lpb">
                                    <option>Silahkan Pilih LPB</option>
                                    @if (count($kitirs))
                                        @foreach ($kitirs as $kitir)
                                            <option value="{{ $kitir['source_type'] }}|{{ $kitir['id'] }}">
                                                {{ $kitir['no_kitir'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <label for="tally_id" class="col-sm-2 col-form-label">Tally</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="tally_id" id="tally_id">
                                    <option value="">Silahkan Pilih Tally</option>
                                    @if (count($rotariEmployees))
                                        @foreach ($rotariEmployees as $rotariEmployee)
                                            <option value="{{ $rotariEmployee->id }}">{{ $rotariEmployee->alias_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if ($type != '5-feet')
                            <div class="form-group row">
                                <label for="wood_type" class="col-sm-2 col-form-label">Jenis Kayu</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="wood_type" name="wood_type"
                                        value="{{ old('wood_type') }}">
                                    <span class="text-danger error-text" id="wood_type_error"></span>
                                </div>
                            </div>
                        @endif
                        <h3>Data Barang</h3>
                        <hr>
                        <span class="text-danger error-text" id="details_error"></span>
                        <div id="handsontable-container" class="d-block"></div>
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
                    $('#tally_id').select2({
                        theme: "bootstrap-5",
                    });

                    $('#no_lpb').select2({
                        theme: "bootstrap-5",
                        placeholder: "Pilih No. LPB",
                        allowClear: true
                    });

                    // handsontable
                    let columnType = [{
                            data: 'no_kitir',
                            type: 'numeric'
                        },
                        {
                            data: 'height',
                            type: 'numeric'
                        },
                        {
                            data: 'width',
                            type: "numeric"
                        },
                        {
                            data: 'length',
                            type: 'numeric'
                        },
                        {
                            data: 'qty',
                            type: 'numeric'
                        },
                        {
                            data: 'grade',
                            type: 'dropdown',
                            source: ['OPC', 'PPC'],
                            trimDropdown: true
                        },
                        {
                            data: 'cubication',
                            type: 'numeric'
                        },
                    ];
                    let datas = [];
                    for (let i = 1; i <= 15; i++) {
                        datas.push({
                            no: i
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
                        // minSpareRows: 1,
                        height: 700, // atau nilai tertentu (misal: 500)
                        data: datas,
                        columns: columnType,
                        // rowHeaders: true,
                        colHeaders: ['No Kitir', 'Tinggi', 'Lebar', 'Panjang', 'Jumlah', 'Grade', "Kubikasi"],
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
                                hot.batch(() => {
                                    changes.forEach(([row, prop, oldVal, newVal]) => {
                                        const height = hot.getDataAtCell(row, 1) || 0;
                                        const width = hot.getDataAtCell(row, 2) || 0;
                                        const length = hot.getDataAtCell(row, 3) || 0;
                                        const qty = hot.getDataAtCell(row, 4) || 0;

                                        hot.setDataAtCell(row, 6,
                                            kubikasiCore(length, width, height, qty),
                                            'kubikasi-update');
                                    });
                                });

                                localStorage.setItem("{{ $type }}", JSON.stringify(data));
                            }
                        }

                    });

                    // Isi data dari localStorage saat halaman dimuat
                    function getLocalStorage() {
                        let columnHeadersType = ['No_kitir', 'length', 'width', 'length', 'qty', 'grade', "cubication"];

                        const savedData = localStorage.getItem("{{ $type }}");


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
                                            localStorage.removeItem("{{ $type }}");
                                            window.location.href = "{{ route('rotari.index', $type) }}";
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
