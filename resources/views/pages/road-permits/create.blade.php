@extends('adminlte::page')

@section('title', 'Buat Surat Jalan')

@section('content_header')
    <h1>Buat Surat Jalan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div id="error-datas" class="alert alert-danger d-none"></div>

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
                            <label for="in" class="col-sm-2 col-form-label">Masuk</label>
                            <div class="col-sm-4">
                                <input type="datetime-local" class="form-control" id="in" name="in"
                                    value="{{ old('in') }}">
                                <span class="text-danger error-text" id="in_error"></span>
                            </div>
                            <label for="out" class="col-sm-2 col-form-label">Keluar</label>
                            <div class="col-sm-4">
                                <input type="datetime-local" class="form-control" id="out" name="out"
                                    value="{{ old('out') }}">
                                <span class="text-danger error-text" id="out_error"></span>
                            </div>
                        </div>
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
                            <label for="from" class="col-sm-2 col-form-label">Pengirim*</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="from" id="from">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
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
                            <label for="vehicle" class="col-sm-2 col-form-label">Kendaraan*</label>
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
                                    <option value="">Pilih Pembongkar</option>
                                    @foreach ($handymans as $handyman)
                                        <option value="{{ $handyman->id }}">{{ $handyman->fullname }}</option>
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
                $('#error-datas').html(''); // Kosongkan error lama setiap submit ulang

                let data = hot.getSourceData();
                data = data.slice(0, -1); // hapus baris kosong terakhir (Handsontable)

                // Validasi muatan kosong
                if (data.length === 0) {
                    Toastify({
                        text: "Muatan tidak boleh kosong!",
                        className: "danger",
                        close: true,
                        style: {
                            background: "red",
                        }
                    }).showToast();
                    document.getElementById('loading').style.display = 'none';
                    return; // hentikan submit
                }

                // Simpan data muatan ke input tersembunyi
                document.getElementById('road_permit_details').value = JSON.stringify(data);

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
                                throw err;
                            }); // ke catch()
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.errors) {
                            // Validasi gagal
                            let errorContainer = $("#error-datas");
                            errorContainer.removeClass("d-none").html(
                                ''); // Tampilkan container dan kosongkan

                            $.each(data.errors, function(key, value) {
                                errorContainer.append(`<div>• ${value.join(', ')}</div>`);
                            });
                        } else {
                            localStorage.removeItem('roadPermitDetails');
                            window.location.href = "{{ route('surat-jalan.index', $type) }}";
                        }
                    })
                    .catch(error => {
                        let errorContainer = $("#error-datas");
                        errorContainer.removeClass("d-none").html('');

                        if (error.message === 'Gagal Simpan Surat Jalan' && error.error) {
                            // Jika error dari catch di controller
                            errorContainer.append(
                                `<strong>${error.message}</strong><br><div>${error.error}</div>`);
                        } else if (error.errors) {
                            // Validasi error (422)
                            Object.entries(error.errors).forEach(([field, msgs]) => {
                                errorContainer.append(`<div>• ${msgs.join(", ")}</div>`);
                            });
                        } else {
                            errorContainer.append(`<div>• Terjadi kesalahan tak dikenal.</div>`);
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
                        theme: "bootstrap-5",
                    }).on('select2:open', function() {
                        // Fokuskan ke input search
                        setTimeout(() => {
                            document.querySelector('.select2-container--open .select2-search__field')
                                ?.focus();
                        }, 10);
                    });;
                    $('#handyman').select2({
                        theme: "bootstrap-5",
                    }).on('select2:open', function() {
                        // Fokuskan ke input search
                        setTimeout(() => {
                            document.querySelector('.select2-container--open .select2-search__field')
                                ?.focus();
                        }, 10);
                    });;
                    $('#from').select2({
                        theme: "bootstrap-5",
                    }).on('select2:open', function() {
                        // Fokuskan ke input search
                        setTimeout(() => {
                            document.querySelector('.select2-container--open .select2-search__field')
                                ?.focus();
                        }, 10);
                    });;

                    @section('plugins.Toast', true)
                        var status = "{{ session('status') }}";
                        if (status == "errDate") {
                            Toastify({
                                text: "Tanggal keluar lebih besar dari pada tanggal masuk",
                                className: "danger",
                                close: true,
                                style: {
                                    background: "red",
                                }
                            }).showToast();
                        }
                    })
    </script>
@stop
