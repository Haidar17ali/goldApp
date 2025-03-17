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
                </tbody>
            </table>

            {{-- data lpb yang telah erpilih --}}
            <div class="container mt-4">
                <h4>Daftar Supplier Terpilih</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Sisa DP</th>
                            <th>Total Kubikasi</th>
                            <th>Total LPB</th>
                            <th>Total PPh 22</th>
                            <th>DP</th>
                            <th>Total Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="selectedSupplierTable">
                        <!-- Data supplier akan muncul di sini -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right;">Grand Total Akhir:</th>
                            <th id="grand-total-akhir">Rp.0</th>
                        </tr>
                    </tfoot>
                </table>
                <h5>DP Menunggu Pembayaran</h5>
                <table class="table table-bordered" id="dp-menunggu-table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Tanggal DP</th>
                            <th>Jumlah DP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Content via JS -->
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
            // ajax select data
            let selectedLPBs = new Set(JSON.parse(localStorage.getItem('selectedLPBs')) || []);
            let selectedLPBData = JSON.parse(localStorage.getItem('selectedLPBData')) || [];
            let debounceTimer;
            let supplierMap = {}; // Global

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

            async function loadDpMenungguPembayaran() {
                let data = {
                    type: 'Menunggu Pembayaran',
                    model: 'Down_payment',
                    relation: [
                        'supplier'
                    ]
                }
                let down_payments = await loadWithData("{{ route('utility.dp-type') }}", data);

                let tbody = $('#dp-menunggu-table tbody');
                tbody.empty(); // Bersihkan isi tabel

                let totalDpMenunggu = 0;

                if (down_payments != null) {

                    down_payments.forEach(dp => {
                        totalDpMenunggu += dp.jumlah;

                        let row = `
                        <tr>
                            <td>${dp.supplier != null ? dp.supplier.name : ""}</td>
                            <td>${dp.date}</td>
                            <td>Rp.${money_format(dp.nominal, 0, ',', '.')}</td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                } else {
                    tbody.append(
                        `<tr><td colspan="4" class="text-center">Tidak ada DP yang menunggu pembayaran</td></tr>`
                    );
                }

                // Tampilkan total DP menunggu di bagian lain atau akumulasi
                $('#total-dp-menunggu').text(
                    `Rp.${money_format(totalDpMenunggu.toLocaleString(), 0, ',', '.')}`);

                // Akumulasi ke Grand Total
                updateGrandTotalAkhir(totalDpMenunggu); // tambahkan parameter
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
                let pph22 = 0;

                if (lpb.details) {
                    lpb.details.forEach(detail => {
                        let myKubikasi = kubikasi(detail.diameter, detail.length, detail.qty);
                        let totalDetail = myKubikasi * detail.price;
                        totalPembayaran += totalDetail;
                        totalKubikasi += parseFloat(myKubikasi);
                    });
                    pph22 = totalPembayaran * 0.0025; // Hitung PPh 22 (2.5%)
                }

                return {
                    totalPembayaran,
                    totalKubikasi,
                    pph22,
                    totalSetelahPph: totalPembayaran - pph22
                };
            }

            function updateSelectedSupplierTable() {
                supplierMap = {}
                if (selectedLPBData.length !== 0) {
                    selectedLPBData.forEach(lpb => {
                        let {
                            totalPembayaran,
                            totalKubikasi,
                            pph22
                        } = calculateTotalPerLPB(lpb);
                        let supplierName = lpb.supplier.name;
                        let supplierId = lpb.supplier.id;
                        let supplierSisaDp = lpb.supplier.sisaDp;

                        if (!supplierMap[supplierName]) {
                            supplierMap[supplierName] = {
                                supplier: supplierName,
                                id: supplierId,
                                totalKubikasi: 0,
                                totalBayar: 0,
                                totalPph22: 0, // Akumulasi PPh 22
                                dp: 0,
                                sisaDp: supplierSisaDp,
                                lpbs: []
                            };
                        }

                        supplierMap[supplierName].totalKubikasi += totalKubikasi;
                        supplierMap[supplierName].totalBayar += totalPembayaran;
                        supplierMap[supplierName].totalPph22 += pph22; // Akumulasi PPh 22
                        supplierMap[supplierName].lpbs.push({
                            ...lpb,
                            totalPembayaran,
                            totalKubikasi,
                            pph22, // Tambahkan PPh 22 ke LPB
                            totalSetelahPph: totalPembayaran - pph22
                        });
                    });
                }
                let tableContent = '';
                Object.entries(supplierMap).forEach(([supplierName, supplier], index) => {
                    let supplierId = `supplier-${index}`;
                    let totalAkhir = supplier.totalBayar - supplier.totalPph22 - supplier.dp;

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
                                            <td>Rp.${money_format(supplier.totalPph22.toFixed(0).toLocaleString(), 0, ',', '.')}</td>
                                            <td>
                                                <input type="number" class="form-control dp-input" data-supplier="${supplier.id}" value="${supplier.dp}">
                                            </td>
                                            <td>
                                                <span class="total-akhir" data-supplier="${supplier.id}">
                                                    Rp.${money_format(totalAkhir.toFixed(0).toLocaleString(), 0, ',', '.')}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <div id="${supplierId}" class="collapse">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Kode LPB</th>
                                                                <th>No Polisi</th>
                                                                <th>Total Kubikasi</th>
                                                                <th>Total Pembayaran</th>
                                                                <th>PPh 22</th>
                                                                <th>Total Setelah PPh 22</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${supplier.lpbs.map(lpb => `
                                                                                                                                                                                                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>${lpb.code}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>${lpb.nopol}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>${lpb.totalKubikasi.toFixed(4)}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>Rp.${money_format(lpb.totalPembayaran.toFixed(0).toLocaleString(), 0, ',', '.')}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>Rp.${money_format(lpb.pph22.toFixed(0).toLocaleString(), 0, ',', '.')}</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <td>Rp.${money_format(lpb.totalSetelahPph.toFixed(0).toLocaleString(), 0, ',', '.')}</td>
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

            function updateGrandTotalAkhir() {
                let grandTotal = 0;

                // Loop semua supplier untuk totalAkhir
                for (let supplierId in supplierMap) {
                    let supplier = supplierMap[supplierId];
                    let totalBayar = supplier.totalBayar || 0;
                    let totalPph22 = supplier.totalPph22 || 0;
                    let dp = supplier.dp || 0;

                    let totalAkhir = totalBayar - totalPph22 - dp;
                    totalAkhir = Math.max(totalAkhir, 0); // pastikan tidak minus

                    grandTotal += totalAkhir;
                }

                // Update tampilan Grand Total Akhir
                $('#grand-total-akhir').text(
                    `Rp.${money_format(grandTotal.toFixed(0).toLocaleString(), 0, ',', '.')}`);
            }

            function updateSupplierMapFromSelectedLPB() {
                supplierMap = {}; // Reset supplierMap
                if (selectedLPBData.length !== 0) {
                    selectedLPBData.forEach(lpb => {
                        let {
                            totalPembayaran,
                            totalKubikasi,
                            pph22
                        } = calculateTotalPerLPB(lpb);
                        let supplierName = lpb.supplier.name;
                        let supplierId = lpb.supplier.id;
                        let supplierSisaDp = lpb.supplier.sisaDp;

                        if (!supplierMap[supplierName]) {
                            supplierMap[supplierName] = {
                                supplier: supplierName,
                                id: supplierId,
                                totalKubikasi: 0,
                                totalBayar: 0,
                                totalPph22: 0,
                                dp: 0,
                                sisaDp: supplierSisaDp,
                                lpbs: []
                            };
                        }

                        supplierMap[supplierName].totalKubikasi += totalKubikasi;
                        supplierMap[supplierName].totalBayar += totalPembayaran;
                        supplierMap[supplierName].totalPph22 += pph22;
                        supplierMap[supplierName].lpbs.push({
                            ...lpb,
                            totalPembayaran,
                            totalKubikasi,
                            pph22,
                            totalSetelahPph: totalPembayaran - pph22
                        });
                    });
                }
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
            updateGrandTotalAkhir();
            loadDpMenungguPembayaran();

            $(document).on('input', '.dp-input', function() {
                let supplierId = $(this).data('supplier'); // Ambil id supplier
                let inputField = $(this); // Ambil field input DP
                let dpAmount = parseFloat($(this).val()) || 0; // DP yang diinput, jika kosong jadi 0

                // Cari data supplier yang sesuai
                let supplier = null;

                Object.values(supplierMap).forEach(sup => {
                    if (sup.id == supplierId) {
                        supplier = sup;
                    }
                });

                if (supplier) {
                    let maxDp = supplier.totalBayar - supplier
                        .totalPph22; // DP maksimum = Total Bayar - PPh22

                    // Cek apakah DP melebihi maksimum
                    if (dpAmount > maxDp) {

                        dpAmount = maxDp; // Set DP ke maksimal
                        inputField.val(maxDp.toFixed(0)); // Update nilai input field
                    }

                    let totalAkhir = supplier.totalBayar - supplier.totalPph22 - dpAmount;

                    // Pastikan Total Akhir minimal 0
                    totalAkhir = Math.max(totalAkhir, 0);

                    // Update tampilan Total Akhir
                    $(`.total-akhir[data-supplier="${supplierId}"]`).text(
                        `Rp.${money_format(totalAkhir.toFixed(0).toLocaleString(), 0, ',', '.')}`);

                    // Update nilai DP di supplierMap (agar nyambung jika dikirim)
                    supplier.dp = dpAmount;
                    supplier.totalAkhir = totalAkhir;
                    updateGrandTotalAkhir();
                }
            });

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
                updateSupplierMapFromSelectedLPB(); // Tambahkan pemanggilan fungsi ini
                updateGrandTotalAkhir()
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
                updateSupplierMapFromSelectedLPB(); // Tambahkan pemanggilan fungsi ini
                updateGrandTotalAkhir()
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
                Object.values(supplierMap).forEach(supplier => {
                    dpData[supplier.id] = supplier.dp; // pakai id sebagai key, dp sebagai value
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
