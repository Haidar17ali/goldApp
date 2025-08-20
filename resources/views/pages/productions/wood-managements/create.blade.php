@extends('adminlte::page')

@section('title', 'Pengeloaan Kayu', $type)

@section('content_header')
    <h1>Pengelolaan Kayu {{ $type }}</h1>
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
            <div class="badge badge-primary float-right">Pengelolaan Kayu {{ $type }}</div>
        </div>
    </div>
    <form action="{{ route('pengelolaan-kayu.simpan', $type) }}" method="POST" id="formRP">
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
                            <label for="shift" class="col-sm-2 col-form-label">Grade</label>
                            <div class="col-sm-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" checked type="radio" name="grade" id="super"
                                        value="Super">
                                    <label class="form-check-label" for="super">Super</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="grade" id="afkir"
                                        value="Afkir">
                                    <label class="form-check-label" for="afkir">Afkir</label>
                                </div>
                                <span class="text-danger error-text" id="shift_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_lpb" class="col-sm-2 col-form-label">No LPB</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="no_kitir" name="no_kitir"
                                    value="{{ old('no_kitir') }}">
                                <span class="text-danger error-text" id="no_kitir_error"></span>
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
                                <label for="shift" class="col-sm-2 col-form-label">Dari</label>
                                <div class="col-sm-4">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="from" id="130"
                                            value="130">
                                        <label class="form-check-label" for="130">130</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" checked type="radio" name="from" id="260"
                                            value="260">
                                        <label class="form-check-label" for="260">260</label>
                                    </div>
                                    <span class="text-danger error-text" id="shift_error"></span>
                                </div>
                                <label for="shift" class="col-sm-2 col-form-label">Ke</label>
                                <div class="col-sm-4">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" checked type="radio" name="to" id="130"
                                            value="130">
                                        <label class="form-check-label" for="130">130</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="to" id="260"
                                            value="260">
                                        <label class="form-check-label" for="260">260</label>
                                    </div>
                                    <span class="text-danger error-text" id="shift_error"></span>
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
                    // Ubah ke format yang dibutuhkan: {id: 1, name: '77216'}
                    const dropdownSource = @json($lpbs->map(fn($lpb) => ['id' => $lpb->id, 'no_kitir' => $lpb->no_kitir]));

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
                            data: 'no_kitir', // atau gunakan index 0 di getDataAtCell seperti kamu
                            type: 'autocomplete',
                            strict: true,
                            allowInvalid: false,
                            source: function(query, callback) {
                                callback(dropdownSource.map(item => item.no_kitir));
                            },
                            renderer: function(instance, td, row, col, prop, value, cellProperties) {
                                // jika value adalah ID -> tampilkan no_kitir
                                let item = dropdownSource.find(opt => opt.id == value);
                                if (!item) {
                                    // mungkin value masih berupa no_kitir saat inisialisasi
                                    item = dropdownSource.find(opt => opt.no_kitir == value);
                                }
                                td.textContent = item ? item.no_kitir : '';
                                return td;
                            }
                        },
                        {
                            data: 'source_diameter',
                            type: 'numeric'
                        },
                        {
                            data: 'source_qty',
                            type: "numeric"
                        },
                        {
                            data: 'conversion_diameter',
                            type: 'numeric'
                        },
                        {
                            data: 'conversion_qty',
                            type: "numeric"
                        },
                    ];
                    let datas = [];
                    for (let i = 1; i <= 30; i++) {
                        datas.push({
                            no: i
                        });
                    }

                    const container = document.getElementById('handsontable-container');
                    let isUpdating = false; // Flag untuk mencegah rekursi
                    const hot = new Handsontable(container, {
                        // minSpareRows: 1,
                        height: 700, // atau nilai tertentu (misal: 500)
                        data: datas,
                        columns: columnType,
                        // rowHeaders: true,
                        colHeaders: ['No Kitir', 'Diameter Sumber', "QTY", "Diameter Konversi", "QTY"],
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
                                        const no_kitir = hot.getDataAtCell(row, 0) || 0;
                                        const source_diameter = hot.getDataAtCell(row, 1) || 0;
                                        const source_qty = hot.getDataAtCell(row, 2) || 0;
                                        const conversion_diameter = hot.getDataAtCell(row, 3) ||
                                            0;
                                        const conversion_qty = hot.getDataAtCell(row, 4) || 0;
                                    });
                                });

                                localStorage.setItem("{{ $type }}", JSON.stringify(data));
                            }
                        }

                    });

                    // Isi data dari localStorage saat halaman dimuat
                    function getLocalStorage() {
                        let columnHeadersType = ['no_kitir', 'source_diameter', 'source_qty', 'conversion_diameter',
                            'conversion_qty'
                        ];

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
                                            window.location.href = "{{ route('pengelolaan-kayu.index', $type) }}";
                                        }
                                    })
                                    .catch(error => {
                                        let errorContainer = document.getElementById("error-datas");

                                        if (!errorContainer) {
                                            console.error("Elemen #error-datas tidak ditemukan di DOM.");
                                        } else if (error?.errors) {
                                            Object.entries(error.errors).forEach(([field, msgs]) => {
                                                let errorText = `${field}: ${msgs.join(" | ")}<br>`;
                                                $("#error-datas").append(errorText);
                                            });
                                        } else {
                                            console.error("Format error tidak seperti yang diharapkan:", error);
                                            $("#error-datas").append("Terjadi kesalahan tidak terduga.");
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
