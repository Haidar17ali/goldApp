@extends('adminlte::page')

@section('title', 'Buat Surat Jalan')

@section('content_header')
    <h1>Buat Surat Jalan</h1>
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
            <div class="badge badge-primary float-right">Buat Surat Jalan</div>
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
                            <label for="from" class="col-sm-2 col-form-label">Pengirim*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="from" name="from"
                                    value="{{ old('from') }}">
                                <span class="text-danger error-text" id="from_error"></span>
                            </div>
                        </div>
                        @if ($type == 'out')
                            <div class="form-group row">
                                <label for="destination" class="col-sm-2 col-form-label">Penerima*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="destination" name="destination"
                                        value="{{ old('destination') }}">
                                    <span class="text-danger error-text" id="destination_error"></span>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="item_type" class="col-sm-2 col-form-label">Jenis Barang</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="item_type" id="item_type">
                                    @foreach ($item_types as $item_type)
                                        <option value="{{ $item_type }}">{{ $item_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="vehicle" class="col-sm-2 col-form-label">Kendaraan</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="vehicle" id="vehicle">
                                    @foreach ($trucks as $truck)
                                        <option value="{{ $truck }}">{{ $truck }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nopol" class="col-sm-2 col-form-label">Nopol*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nopol" name="nopol"
                                    value="{{ old('nopol') }}">
                                <span class="text-danger error-text" id="nopol_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="driver" class="col-sm-2 col-form-label">Soper*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="driver" name="driver"
                                    value="{{ old('driver') }}">
                                <span class="text-danger error-text" id="driver_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="handyman" class="col-sm-2 col-form-label">Pembongkar</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="handyman" id="handyman">
                                    @foreach ($trucks as $truck)
                                        <option value="{{ $truck }}">{{ $truck }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location" class="col-sm-2 col-form-label">Lokasi Bongkar</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="location" id="location">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                            <label for="sub_number" class="col-sm-2 col-form-label">Nomer Sub</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="sub_number" id="sub_number">
                                    <option value="">Pilih Sub</option>
                                    @for ($i = 1; $i <= 40; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                        </div>
                        <div class="form-group row" id="sill_number_group">
                            <label for="sill_number" class="col-sm-2 col-form-label">Nomer Sill</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="sill_number" name="sill_number"
                                    value="{{ old('sill_number') }}">
                            </div>
                        </div>
                        <div class="form-group row" id="container_number_group">
                            <label for="container_number" class="col-sm-2 col-form-label">Nomer Container</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="container_number" name="container_number"
                                    value="{{ old('container_number') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" id="description" name="description"
                                    value="{{ old('description') }}"></textarea>
                            </div>
                        </div>
                        <h3>Data Muatan</h3>
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
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
                    // handsontable
                    const container = document.getElementById('handsontable-container');
                    const hot = new Handsontable(container, {
                        minSpareRows: 1,
                        data: [],
                        columns: [{
                                data: 'load',
                                type: 'text'
                            },
                            {
                                data: 'amount',
                                type: 'numeric'
                            },
                            {
                                data: 'unit',
                                type: 'text'
                            },
                            {
                                data: 'size',
                                type: 'text'
                            },
                            {
                                data: 'cubication',
                                type: 'numeric'
                            },
                        ],
                        rowHeaders: true,
                        colHeaders: ['Muatan', 'Jumlah', 'Satuan', 'Ukuran', 'Kubikasi'],
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

                    // Fungsi untuk menyesuaikan tampilan berdasarkan jenis barang
                    function toggleFieldsByItemType() {

                        const itemType = $('#item_type').val();

                        if (itemType === 'Sengon') {
                            // Sembunyikan input sill dan container
                            $('#sill_number').closest('.form-group').hide();
                            $('#container_number').closest('.form-group').hide();

                            // Atur ulang Handsontable hanya untuk Muatan dan Jumlah
                            hot.updateSettings({
                                columns: [{
                                        data: 'load',
                                        type: 'text'
                                    },
                                    {
                                        data: 'amount',
                                        type: 'numeric'
                                    }
                                ],
                                colHeaders: ['Muatan', 'Jumlah']
                            });

                            // Isi data default khusus sengon
                            hot.loadData([{
                                    load: 'afkir',
                                    amount: 0
                                },
                                {
                                    load: '130',
                                    amount: 0
                                },
                                {
                                    load: '260',
                                    amount: 0
                                }
                            ]);

                        } else {
                            // Tampilkan kembali input sill dan container
                            $('#sill_number').closest('.form-group').show();
                            $('#container_number').closest('.form-group').show();

                            // Reset kolom Handsontable ke default
                            hot.updateSettings({
                                columns: [{
                                        data: 'load',
                                        type: 'text'
                                    },
                                    {
                                        data: 'amount',
                                        type: 'numeric'
                                    },
                                    {
                                        data: 'unit',
                                        type: 'text'
                                    },
                                    {
                                        data: 'size',
                                        type: 'text'
                                    },
                                    {
                                        data: 'cubication',
                                        type: 'numeric'
                                    }
                                ],
                                colHeaders: ['Muatan', 'Jumlah', 'Satuan', 'Ukuran', 'Kubikasi']
                            });

                            // Kosongkan data handsontable
                            hot.loadData([]);
                        }
                    }

                    // Jalankan fungsi saat halaman dimuat dan saat jenis barang diubah
                    toggleFieldsByItemType();
                    $('#item_type').on('change', toggleFieldsByItemType);

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
    <script>
        $(document).ready(function() {
            $('#location').select2({
                theme: "bootstrap4",
            });
        })
    </script>
@stop
