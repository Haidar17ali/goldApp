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
        <div class="card-header row">
            <div class="col-md-3 offset-9 float-right">
                <input type="text" id="searchBox" class="form-control mb-3 float-right" placeholder="Cari LPB...">
            </div>
        </div>
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
                <tbody id="lpbTable">
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

            {{-- data lpb yang telah erpilih --}}
            <div class="container mt-4">
                <h4>Daftar Supplier Terpilih</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Sisa Uang Muka</th>
                            <th>Total Kubikasi</th>
                            <th>Total yang Harus Dibayar</th>
                            <th>Uang Muka yang akan dibayar</th>
                        </tr>
                    </thead>
                    <tbody id="selectedSupplierTable">
                        <!-- Data supplier akan muncul di sini -->
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                <a href="{{ route('purchase-jurnal.index') }}" class="btn btn-danger">Batal</a>
                <button class="btn btn-primary" id="kirimPembayaranBtn">Simpan</button>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script src="{{ asset('assets/js/myHelper.js') }}"></script>

    <script>
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
            });

            // ajax select data
            let selectedLPBs = new Set(JSON.parse(localStorage.getItem('selectedLPBs')) || []);
            let selectedLPBData = JSON.parse(localStorage.getItem('selectedLPBData')) || [];
            let debounceTimer;

            async function loadLpbs(search = '') {
                try {
                    let data = {
                        search: search,
                        from: 'LPB',
                    }
                    let datas = await loadWithData("{{ route('search') }}", data);

                    let tableContent = '';
                    datas.forEach(lpb => {
                        let isChecked = selectedLPBs.has(lpb.id.toString());
                        // untuk edit data yang di filter
                        let editUrl = "{{ route('lpb.ubah', ':id') }}";
                        editUrl = editUrl.replace(':id', lpb.id)

                        let totals = {
                            totalAfkir: 0,
                            total130: 0,
                            total260: 0
                        };

                        if (lpb.details) { // Pastikan details ada
                            totals = lpb.details.reduce((acc, detail) => {
                                if (detail.quality == 'Afkir') acc.totalAfkir +=
                                    parseInt(detail.qty) || 0;
                                if (detail.length == '130' && detail.quality ==
                                    'Super') acc.total130 +=
                                    parseInt(detail.qty) || 0;
                                if (detail.length == '260' && detail.quality ==
                                    'Super') acc.total260 +=
                                    parseInt(detail.qty) || 0;
                                return acc;
                            }, {
                                totalAfkir: 0,
                                total130: 0,
                                total260: 0
                            });
                        }

                        tableContent += `
                    <tr>
                        <td>${lpb.code}</td>
                        <td>${lpb.supplier != null ? lpb.supplier.name : "Supplier Tidakditemukan"}</td>
                        <td>${lpb.nopol}</td>
                        <td>${totals.totalAfkir}</td>
                        <td>${totals.total130}</td>
                        <td>${totals.total260}</td>
                        <td>${nominalKubikasi(lpb.details)}</td>
                        <td>
                            <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                            <a href="${editUrl}" class="btn btn-success btn-sm"><i class="fas fa-edit"></i></a>
                            <button class="btn ${isChecked ? 'btn-success' : 'btn-secondary'} btn-sm select-btn" data-id="${lpb.id}" 
                                data-lpb='${JSON.stringify(lpb)}'>
                                <i class="fas ${isChecked ? 'fa-check' : 'fa-square'}"></i>
                            </button>
                        </td>
                    </tr>
                `;
                    });
                    $('#lpbTable').html(tableContent);

                    $('#loading').hide(); // 🔹 Sembunyikan loading setelah pencarian selesai
                } catch (error) {
                    console.error("Gagal memuat data:", error);
                }
            }

            function updateSelectedLPBTable() {
                let tableBody = $("#selectedLPBTable tbody");
                tableBody.empty(); // Bersihkan isi tabel sebelum menambahkan ulang

                if (selectedLPBData.length === 0) {
                    tableBody.append('<tr><td colspan="3" class="text-center">Tidak ada LPB terpilih</td></tr>');
                    return;
                }

                selectedLPBData.forEach(lpb => {
                    let row = `
                                <tr>
                                    <td>${lpb.id}</td>
                                    <td>${lpb.total_pembayaran}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm remove-lpb-btn" data-id="${lpb.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    tableBody.append(row);
                });
                restoreCollapseState(); // Pastikan collapse tetap terbuka
            }

            function updateDPSupplier(suppliers) {
                if (suppliers.size === 0) {
                    $('#dpSupplier').html("<p>Tidak ada supplier untuk DP</p>");
                    return;
                }

                let supplierHTML = `<h4>Masukkan DP untuk Supplier</h4>`;
                suppliers.forEach((supplier, name) => {
                    supplierHTML += `
                                    <div class="form-group">
                                        <label>${name}</label>
                                        <input type="number" class="form-control dp-input" data-supplier="${id}" placeholder="Masukkan DP untuk ${id}">
                                    </div>
                                `;
                });

                $('#dpSupplier').html(supplierHTML);
            }

            function updateLPBTableUI() {
                $('.select-btn').each(function() {
                    let lpbId = $(this).data('id').toString();
                    let isSelected = selectedLPBs.has(lpbId);

                    // Ubah warna tombol
                    $(this).toggleClass('btn-success', isSelected);
                    $(this).toggleClass('btn-secondary', !isSelected);

                    // Ubah ikon dalam tombol
                    $(this).find('i').toggleClass('fa-check', isSelected);
                    $(this).find('i').toggleClass('fa-square', !isSelected);
                });
            }

            function calculateTotalPerLPB(lpb) {
                let totalPembayaran = 0;
                let totalKubikasi = 0;

                if (lpb.details) {
                    lpb.details.forEach(detail => {

                        let myKubikasi = kubikasi(detail.diameter, detail.length, detail.qty);
                        let totalDetail = myKubikasi * detail.price; // Kubikasi × Harga
                        totalPembayaran += totalDetail;
                        totalKubikasi += parseFloat(myKubikasi);
                    });
                }

                // 🔹 Kembalikan sebagai objek agar bisa digunakan di tempat lain
                return {
                    totalPembayaran,
                    totalKubikasi
                };
            }

            function updateSelectedSupplierTable() {
                let supplierMap = {};

                if (selectedLPBData.length !== 0) {
                    selectedLPBData.forEach(lpb => {
                        let {
                            totalPembayaran,
                            totalKubikasi
                        } = calculateTotalPerLPB(lpb);
                        let supplierName = lpb.supplier.name;
                        let supplierId = lpb.supplier.id; // Ambil ID supplier
                        let supplierSisaDp = lpb.supplier.sisaDp;

                        if (!supplierMap[supplierName]) {
                            supplierMap[supplierName] = {
                                supplier: supplierName,
                                id: supplierId, // Tambahkan ID supplier
                                totalKubikasi: 0,
                                totalBayar: 0,
                                dp: 0,
                                sisaDp: supplierSisaDp,
                                lpbs: []
                            };
                        }

                        supplierMap[supplierName].totalKubikasi += totalKubikasi;
                        supplierMap[supplierName].totalBayar += totalPembayaran;
                        supplierMap[supplierName].lpbs.push({
                            ...lpb,
                            totalPembayaran,
                            totalKubikasi
                        });
                    });
                }

                let tableContent = '';
                Object.entries(supplierMap).forEach(([supplierName, supplier], index) => {
                    let supplierId = `supplier-${index}`;

                    tableContent += `
                                    <tr>
                                        <td>
                                            <button class="btn btn-link supplier-btn" type="button" data-toggle="collapse" data-target="#${supplierId}">
                                                ${supplier.supplier}
                                            </button>
                                        </td>
                                        <td>Rp.${money_format(supplier.sisaDp, 0, ',', '.')}</td>
                                        <td>${supplier.totalKubikasi.toFixed(4)}</td>
                                        <td>Rp.${supplier.totalBayar.toLocaleString()}</td>
                                        <td><input type="number" class="form-control dp-input" data-supplier="${supplier.id}" value="${supplier.dp}"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            <div id="${supplierId}" class="collapse">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Kode LPB</th>
                                                            <th>No Polisi</th>
                                                            <th>Total Kubikasi</th>
                                                            <th>Total Pembayaran</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${supplier.lpbs.map(lpb => `
                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                <td>${lpb.code}</td>
                                                                                                                                                                                                                <td>${lpb.nopol}</td>
                                                                                                                                                                                                                <td>${lpb.totalKubikasi.toFixed(4)}</td>
                                                                                                                                                                                                                <td>${lpb.totalPembayaran.toLocaleString()}</td>
                                                                                                                                                                                                                <td><button class="btn btn-danger btn-sm remove-lpb-btn" data-id="${lpb.id}"><i class="fas fa-trash"></i></button></td>
                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                        `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                });

                $('#selectedSupplierTable').html(tableContent);
                restoreCollapseState();
            }

            function restoreSelectedLPBs() {
                selectedLPBs = new Set(JSON.parse(localStorage.getItem('selectedLPBs')) || []);
                selectedLPBData = JSON.parse(localStorage.getItem('selectedLPBData')) || [];

                // 🔹 Perbarui tampilan daftar yang sudah terpilih
                updateSelectedLPBTable();
                updateLPBTableUI();
                updateSelectedSupplierTable();
            }

            function restoreCollapseState() {
                let collapseState = JSON.parse(localStorage.getItem('collapseState')) || {};

                $(".collapse").each(function() {
                    let collapseId = $(this).attr('id');
                    if (collapseState[collapseId]) {
                        $(this).addClass("show");
                    } else {
                        $(this).removeClass("show");
                    }
                });
            }

            // tampilan bootstrap collapse
            $(document).on('shown.bs.collapse hidden.bs.collapse', '.collapse', function(e) {
                let collapseId = $(this).attr('id');
                let isOpen = $(this).hasClass('show');

                let collapseState = JSON.parse(localStorage.getItem('collapseState')) || {};
                collapseState[collapseId] = isOpen;
                localStorage.setItem('collapseState', JSON.stringify(collapseState));
            });

            loadLpbs();
            restoreSelectedLPBs(); // 🔹 Memulihkan data LPB yang dipilih
            updateLPBTableUI();
            updateSelectedLPBTable();

            $('#searchBox').on('input', function() {

                clearTimeout(debounceTimer); // Hapus timer sebelumnya
                $('#loading').show(); // 🔹 Tampilkan loading sebelum pencarian

                debounceTimer = setTimeout(() => {
                    loadLpbs($(this).val())

                    $('#loading').hide(); // 🔹 Tampilkan loading sebelum pencarian
                }, 500); // Tunggu 300ms setelah pengguna berhenti mengetik

            });


            $(document).on('click', '.select-btn', function() {
                let lpbId = $(this).data('id').toString();
                let lpb = JSON.parse($(this).attr('data-lpb'));

                let existingLPB = selectedLPBData.find(item => item.id.toString() === lpbId);

                if (existingLPB) {
                    selectedLPBs.delete(lpbId);
                    selectedLPBData = selectedLPBData.filter(item => item.id.toString() !== lpbId);
                } else {
                    selectedLPBs.add(lpbId);
                    selectedLPBData.push(lpb);
                }

                localStorage.setItem('selectedLPBs', JSON.stringify(Array.from(selectedLPBs)));
                localStorage.setItem('selectedLPBData', JSON.stringify(selectedLPBData));

                updateLPBTableUI();
                updateSelectedLPBTable();
                updateSelectedSupplierTable();
            });

            $(document).on('click', '.remove-lpb-btn', function() {
                let lpbId = $(this).data('id').toString();

                // Hapus LPB dari daftar terpilih
                selectedLPBs.delete(lpbId);
                selectedLPBData = selectedLPBData.filter(item => item.id.toString() !== lpbId);

                // Simpan kembali ke localStorage
                localStorage.setItem('selectedLPBs', JSON.stringify(Array.from(selectedLPBs)));
                localStorage.setItem('selectedLPBData', JSON.stringify(selectedLPBData));

                // Update tampilan
                updateLPBTableUI();
                updateSelectedLPBTable();
                updateSelectedSupplierTable();
            });

            $(document).on('input', '.dp-input', function() {
                let supplierName = $(this).data('supplier');
                let dpAmount = $(this).val();
            });

            function sendDataToLaravel(selectedLPBData, dpData) {
                $.ajax({
                    url: "{{ route('purchase-jurnal.simpan') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: "{{ csrf_token() }}",
                        lpbs: selectedLPBData,
                        dp: dpData
                    },
                    success: function(response) {
                        window.location.href = "{{ route('purchase-jurnal.index') }}";
                    },
                    error: function(error) {
                        console.error("Gagal mengirim data:", error);
                        alert("Terjadi kesalahan saat menyimpan pembayaran");
                    }
                });
            }

            function collectDpData() {
                let dpData = {};
                $('.dp-input').each(function() {
                    let supplierName = $(this).data('supplier');
                    let idSupp = $(this).data('supplier');
                    let dpAmount = $(this).val();

                    dpData[supplierName] = dpAmount;
                });
                return dpData;
            }

            $(document).on('click', '#kirimPembayaranBtn', function() {
                let dpData = collectDpData();

                sendDataToLaravel(selectedLPBData, dpData);
            });
        });
    </script>
@stop
