@extends('adminlte::page')

@section('title', 'Buat Purchase Order ' . $type)

@section('content_header')
    <h1>Buat Purchase Order {{ $type }}</h1>
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

            <div id="error-messages"></div>
            <div class="badge badge-primary float-right">Buat Purchase Order {{ $type }}</div>
        </div>
    </div>
    <form action="{{ route('surat-jalan.simpan', $type) }}" method="POST" id="formRP">
        @csrf
        @method('post')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="supplier_id" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="supplier_id" id="supplier_id">
                                    @if (count($suppliers))
                                        @foreach ($suppliers as $supplier->name)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if ($type == 'Bahan-Baku')
                            <div class="form-group row">
                                <label for="supplier_id" class="col-sm-2 col-form-label">Tipe Supplier</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="supplier_id" id="supplier_id">
                                        @if (count($supplier_types))
                                            @foreach ($supplier_types as $supplier_type)
                                                <option value="{{ $supplier_type }}">{{ $supplier_type }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @endif
                        @if ($type != 'Bahan-Baku')
                            <div class="form-group row">
                                <label for="dp" class="col-sm-2 col-form-label">DP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="dp" name="dp"
                                        value="{{ old('dp') }}">
                                    <span class="text-danger error-text" id="dp_error"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ppn" class="col-sm-2 col-form-label">PPN</label>
                                <div class="col-sm-10">
                                    <div class="toggle-container col-md-3">
                                        <!-- Tombol Toggle -->
                                        <label class="switch">
                                            <input type="checkbox" id="ppn" name="permissions[]">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <h3>Data Barang</h3>
                        <hr>
                        <span class="text-danger error-text" id="road_permit_details_error"></span>
                        <div id="error-datas" style="color: red; margin-bottom: 10px;"></div>
                        <div id="handsontable-container"></div>
                        <input type="hidden" name="road_permit_details[]" id="road_permit_details">

                        <div class="float-right mt-3">
                            <a href="{{ route('surat-jalan.index', $type) }}"
                                class="btn btn-danger rounded-pill mr-2">Batal</a>
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

    <script>
        $(document).ready(function() {
                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                    });
                    // handsontable
                    let type = "{{ $type }}";
                    let colHeadersType = ['Nama Barang', 'Jumlah', 'Harga', 'Total'];
                    let columnType = [{
                            data: 'name',
                            type: 'text',
                        },
                        {
                            data: 'quantity',
                            type: 'numeric',
                            numericFormat: {
                                pattern: '0,0.00'
                            },
                        },
                        {
                            data: 'price',
                            type: 'numeric',
                        },
                    ];

                    if (type == "Bahan-Baku") {
                        colHeadersType = ['Diameter Awal', 'Diameter Akhir', 'Harga'];
                        columnType = [{
                                data: 'diameter_start',
                                type: 'numeric',
                            },
                            {
                                data: 'diameter_to',
                                type: 'numeric',
                            },
                            {
                                data: 'price',
                                type: 'numeric',
                            },
                        ];;
                    }

                    const container = document.getElementById('handsontable-container');
                    const hot = new Handsontable(container, {
                        minSpareRows: 1,
                        data: [],
                        columns: columnType,
                        rowHeaders: true,
                        colHeaders: colHeadersType,
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
                                localStorage.setItem('roadPermitDetails', JSON.stringify(data));
                            }
                        },
                    });

                    // Isi data dari localStorage saat halaman dimuat
                    function getLocalStorage() {
                        const savedData = localStorage.getItem('roadPermitDetails');

                        if (savedData != null) {
                            try {
                                const parsedData = JSON.parse(savedData);

                                const columnHeaders = ["load", "amount", "unit", "size",
                                    "cubication"
                                ]; // Sesuaikan dengan jumlah kolom
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


                                let permits = document.getElementById('road_permit_details').value =
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
                                            localStorage.removeItem('roadPermitDetails');
                                            window.location.href = "{{ route('surat-jalan.index', $type) }}";
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
