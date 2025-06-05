@extends('adminlte::page')

@section('title', 'Ubah Surat Jalan')

@section('content_header')
    <h1>Ubah Surat Jalan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div id="error-datas" class="alert alert-danger d-none"></div>
            <div class="badge badge-primary float-right">Ubah Surat Jalan</div>
        </div>
    </div>
    <form action="{{ route('surat-jalan.update', ['id' => $road_permit->id, 'type' => $type]) }}" method="POST"
        id="formRP">
        @csrf
        @method('patch')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="in" class="col-sm-2 col-form-label">Masuk</label>
                            <div class="col-sm-4">
                                @php
                                    $datetimeValue = old(
                                        'in',
                                        \Carbon\Carbon::parse($road_permit->date . ' ' . $road_permit->in)->format(
                                            'Y-m-d\TH:i',
                                        ),
                                    );
                                @endphp
                                <input type="datetime-local" class="form-control" id="in" name="in"
                                    value="{{ $datetimeValue }}">
                                <span class="text-danger error-text" id="in_error"></span>
                            </div>
                            <label for="out" class="col-sm-2 col-form-label">Keluar</label>
                            <div class="col-sm-4">
                                <input type="datetime-local" class="form-control" id="out" name="out"
                                    value="{{ old('out', $road_permit->out) }}">
                                <span class="text-danger error-text" id="out_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="item_type" class="col-sm-2 col-form-label">Jenis Barang</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="item_type" id="item_type">
                                    @foreach ($item_types as $item_type)
                                        <option {{ $item_type == $road_permit->type_item ? 'selected' : '' }}
                                            value="{{ $item_type }}">{{ $item_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="from" class="col-sm-2 col-form-label">Pengirim*</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="from" id="from">
                                    @foreach ($suppliers as $supplier)
                                        <option {{ $supplier->name == $road_permit->from ? 'selected' : '' }}
                                            value="{{ $supplier->name }}">{{ $supplier->name }}</option>
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
                                        value="{{ old('destination', $road_permit->destination) }}">
                                    <span class="text-danger error-text" id="destination_error"></span>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label for="vehicle" class="col-sm-2 col-form-label">Kendaraan</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="vehicle" id="vehicle">
                                    @foreach ($trucks as $truck)
                                        <option {{ $truck == $road_permit->vehicle ? 'selected' : '' }}
                                            value="{{ $truck }}">{{ $truck }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nopol" class="col-sm-2 col-form-label">Nopol*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nopol" name="nopol"
                                    value="{{ old('nopol', $road_permit->nopol) }}">
                                <span class="text-danger error-text" id="nopol_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="driver" class="col-sm-2 col-form-label">Soper*</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="driver" name="driver"
                                    value="{{ old('driver', $road_permit->driver) }}">
                                <span class="text-danger error-text" id="driver_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="handyman" class="col-sm-2 col-form-label">Pembongkar</label>
                            <div class="col-sm-10">
                                <select {{ $road_permit->handyman_id == '' ? 'selected' : '' }} class="form-control"
                                    name="handyman" id="handyman">
                                    <option value="">Pilih Pembongkar</option>
                                    @foreach ($handymans as $handyman)
                                        <option {{ $road_permit->handyman_id == $handyman->id ? 'selected' : '' }}
                                            value="{{ $handyman->id }}">{{ $handyman->fullname }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                        </div>
                        @php
                            $locate = explode('-', $road_permit->unpack_location);
                        @endphp
                        <div class="form-group row">
                            <label for="location" class="col-sm-2 col-form-label">Lokasi Bongkar</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="location" id="location">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach ($locations as $location)
                                        <option {{ $locate[0] == $location ? 'selected' : '' }}
                                            value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                            <label for="sub_number" class="col-sm-2 col-form-label">Nomer Sub</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="sub_number" id="sub_number">
                                    <option value="">Pilih Sub</option>
                                    @for ($i = 1; $i <= 40; $i++)
                                        <option {{ $locate[1] == $i ? 'selected' : '' }} value="{{ $i }}">
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                                <span class="text-danger error-text" id="handyman_error"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sill_number" class="col-sm-2 col-form-label">Nomer Sill</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="sill_number" name="sill_number"
                                    value="{{ old('sill_number', $road_permit->sill_number) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="container_number" class="col-sm-2 col-form-label">Nomer Container</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="container_number" name="container_number"
                                    value="{{ old('container_number', $road_permit->container_number) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" id="description" name="description"
                                    value="{{ old('description', $road_permit->description) }}"></textarea>
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
                        localStorage.setItem('editRoadPermitDetails', JSON.stringify(data));
                    }
                },
            });

            // function setLocalStorage() {
            //     const data = {!! $road_permit->details != null ? json_encode($road_permit->details) : '' !!}

            //     localStorage.setItem('editRoadPermitDetils', JSON.stringify(data));
            // }

            // setLocalStorage();

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
                const savedData = localStorage.getItem('editRoadPermitDetails');

                if (savedData) {
                    try {
                        const parsedData = JSON.parse(savedData);
                        const columnHeaders = ["load", "amount", "unit", "size",
                            "cubication"
                        ]; // Sesuaikan dengan jumlah kolom
                        if (Array.isArray(parsedData)) {
                            if (typeof parsedData[0] === 'object' && !Array.isArray(parsedData[0])) {
                                hot.loadData(parsedData);
                            } else {
                                const formattedData = parsedData.map(row => {
                                    let obj = {};
                                    if (Array.isArray(row)) {
                                        row.forEach((value, index) => {
                                            obj[columnHeaders[index]] =
                                                value; // Konversi array ke objek dengan key yang benar
                                        });
                                        return obj;
                                    }
                                });
                                hot.loadData(formattedData); // Memuat data ke Handsontable
                            }
                        }

                    } catch (error) {
                        console.error("Gagal memuat data dari localStorage:", error);
                    }
                } else {
                    // Jika tidak ada data di localStorage, gunakan data awal dari database
                    const initialData = {!! $road_permit->details != null ? json_encode($road_permit->details) : [] !!}

                    hot.loadData(initialData);
                    localStorage.setItem('editRoadPermitDetails', JSON.stringify(initialData));
                }
            };

            $('#formRP').on('submit', function(e) {
                e.preventDefault();
                document.getElementById('loading').style.display = 'flex';
                $('#error-datas').html('').addClass('d-none'); // Kosongkan error lama setiap submit ulang

                let data = hot.getSourceData();
                data = data.slice(0, -1); // hapus baris kosong terakhir

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
                    return;
                }

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
                    .then(async response => {
                        const data = await response.json();

                        if (!response.ok) {
                            let errorContainer = $("#error-datas");
                            errorContainer.removeClass("d-none").html('');

                            // Jika validasi Laravel gagal (status 422)
                            if (response.status === 422 && data.errors) {
                                Object.entries(data.errors).forEach(([field, msgs]) => {
                                    errorContainer.append(
                                        `<div>• ${msgs.join(", ")}</div>`);
                                });
                            }
                            // Jika error kustom dari backend (500 atau error lainnya)
                            else if (data.message || data.error) {
                                errorContainer.append(
                                    `<strong>${data.message || 'Error'}</strong><br><div>${data.error || '-'}</div>`
                                );
                            } else {
                                errorContainer.append(
                                    `<div>• Terjadi kesalahan tak dikenal.</div>`);
                            }

                            throw new Error('Handled error');
                        }

                        // Jika sukses
                        localStorage.removeItem('roadPermitDetails');
                        window.location.href = "{{ route('surat-jalan.index', $type) }}";
                    })
                    .catch(error => {
                        if (error.message !== 'Handled error') {
                            // Jika error tidak ditangani sebelumnya
                            let errorContainer = $("#error-datas");
                            errorContainer.removeClass("d-none").html(
                                `<div>• Terjadi kesalahan tak dikenal.</div>`);
                            console.error("Uncaught error:", error);
                        }
                    })
                    .finally(() => {
                        document.getElementById('loading').style.display = 'none';
                    });
            });


            getLocalStorage();
        });
        @section('plugins.Toast', true)
            var status = "{{ session('status') }}";
            if (status == "detailsNotFound") {
                Toastify({
                    text: "Muatan tidak boleh kosong!",
                    className: "danger",
                    close: true,
                    style: {
                        background: "#red",
                    }
                }).showToast();
            }
    </script>
@stop
