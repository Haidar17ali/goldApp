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
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination" class="col-sm-2 col-form-label">Penerima*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="destination" name="destination"
                                    value="{{ old('destination') }}">
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
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="driver" class="col-sm-2 col-form-label">Soper*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="driver" name="driver"
                                    value="{{ old('driver') }}">
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
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="unpack_location" class="col-sm-2 col-form-label">Lokasi Bongkar</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="unpack_location" name="unpack_location"
                                    value="{{ old('unpack_location') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sill_number" class="col-sm-2 col-form-label">Nomer Sill</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="sill_number" name="sill_number"
                                    value="{{ old('sill_number') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="container_number" class="col-sm-2 col-form-label">Nomer Container</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="container_number" name="container_number"
                                    value="{{ old('container_number') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" id="description" name="description" value="{{ old('description') }}"></textarea>
                            </div>
                        </div>
                        <h3>Data Muatan</h3>
                        <hr>
                        <div id="handsontable-container"></div>
                        <input type="hidden" name="details[]" id="details">

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

            // Isi data dari localStorage saat halaman dimuat
            function getLocalStorage() {
                const savedData = localStorage.getItem('roadPermitDetails');
                if (savedData) {
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
                const form = document.getElementById('formRP');
                const errorMessages = document.getElementById('error-messages');
                event.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.errors ||
                                    'Terjadi kesalahan. Silakan coba lagi nanti.'
                                ); // Beri pesan default jika tidak ada pesan dari server
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);

                        if (data.status === 'error') {

                            console.error('Error dari server:', data
                                .message); // Tampilkan pesan error dari server di console

                            errorMessages.innerHTML = '';

                            if (data.errors) { // Periksa apakah ada error validasi

                                for (const key in data.errors) {
                                    const errors = data.errors[key];
                                    errors.forEach(error => {
                                        const errorMessage = document.createElement('p');
                                        errorMessage.textContent = error;
                                        errorMessages.appendChild(errorMessage);
                                    });
                                }
                            } else { // Jika tidak ada error validasi, tampilkan pesan error umum dari server
                                const errorMessage = document.createElement('p');
                                errorMessage.textContent = data.message ||
                                    'Terjadi kesalahan. Silakan coba lagi nanti.';
                                errorMessages.appendChild(errorMessage);
                            }

                            const oldInput = data.oldInput;
                            for (const key in oldInput) {
                                const inputElement = document.getElementById(key);
                                if (inputElement) {
                                    inputElement.value = oldInput[key];
                                }
                            }
                        } else if (data.status === 'success') {
                            window.location.href = '/success';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorMessages.innerHTML = '<p>' + error.message +
                            '</p>'; // Tampilkan pesan error yang ditangkap di catch block
                    });


            });

            getLocalStorage();
        });
    </script>
@stop
