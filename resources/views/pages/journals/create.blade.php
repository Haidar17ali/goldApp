@extends('adminlte::page')

@section('title', 'Buat Jurnal')

@section('content_header')
    <h1>Buat Jurnal Umum</h1>
@stop

@section('content')

    <form action="{{ route('jurnal.simpan') }}" method="POST">
        @csrf

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Form Jurnal</h3>

                @if ($errors->any())
                    <div class="alert alert-danger">

                        <h5>
                            <i class="icon fas fa-ban"></i>
                            Terjadi kesalahan!
                        </h5>

                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif
            </div>

            <div class="card-body">

                {{-- HEADER --}}
                <div class="mb-4 row">

                    <div class="col-md-3">
                        <label>Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}"
                            required>
                    </div>

                    {{-- <div class="col-md-3">
                        <label>Referensi</label>
                        <input type="text" name="reference" class="form-control">
                    </div> --}}

                    <div class="col-md-6">
                        <label>Deskripsi</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                    </div>

                </div>

                {{-- DETAIL TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered" id="journal-table">

                        <thead class="thead-light">
                            <tr>
                                <th>Akun</th>
                                <th width="180">Debit</th>
                                <th width="180">Kredit</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php
                                $oldItems = old('items', [['account_id' => '', 'debit' => 0, 'credit' => 0]]);
                            @endphp

                            @foreach ($oldItems as $i => $item)
                                <tr>

                                    <td>
                                        <select name="items[{{ $i }}][account_id]"
                                            class="form-control account-select" required>

                                            <option value="">-- Pilih Akun --</option>

                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}"
                                                    {{ ($item['account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->code }} - {{ $acc->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" name="items[{{ $i }}][debit]"
                                            class="form-control debit" step="0.01" value="{{ $item['debit'] ?? 0 }}">
                                    </td>

                                    <td>
                                        <input type="number" name="items[{{ $i }}][credit]"
                                            class="form-control credit" step="0.01" value="{{ $item['credit'] ?? 0 }}">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            X
                                        </button>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                <button type="button" class="mb-3 btn btn-success" id="add-row">
                    + Tambah Baris
                </button>

                {{-- TOTAL --}}
                <div class="row">

                    <div class="col-md-6 offset-md-6">

                        <table class="table table-bordered">

                            <tr>
                                <th>Total Debit</th>
                                <td id="total-debit">0</td>
                            </tr>

                            <tr>
                                <th>Total Kredit</th>
                                <td id="total-credit">0</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td id="balance-status">
                                    <span class="badge badge-danger">
                                        Tidak Balance
                                    </span>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

            <div class="text-right card-footer">
                <button class="btn btn-primary">
                    Simpan Jurnal
                </button>
            </div>

        </div>

    </form>

@stop


{{-- @section('js')
    <script>
        let rowIndex = {{ count(old('items', [['']])) }};

        // 🔹 Tambah baris
        document.getElementById('add-row').addEventListener('click', function() {

            const tableBody = document.querySelector('#journal-table tbody');

            const row = `
        <tr>
            <td>
                <select name="items[${rowIndex}][account_id]"
                        class="form-control account-select"
                        required>

                    <option value="">-- Pilih Akun --</option>

                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach

                </select>
            </td>

            <td>
                <input type="number"
                       name="items[${rowIndex}][debit]"
                       class="form-control debit"
                       step="0.01"
                       value="0">
            </td>

            <td>
                <input type="number"
                       name="items[${rowIndex}][credit]"
                       class="form-control credit"
                       step="0.01"
                       value="0">
            </td>

            <td class="text-center">
                <button type="button"
                        class="btn btn-danger btn-sm remove-row">
                    X
                </button>
            </td>
        </tr>
    `;

            tableBody.insertAdjacentHTML('beforeend', row);

            rowIndex++;

        });


        // 🔹 Hapus baris
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });


        // 🔹 Hitung total
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('debit') ||
                e.target.classList.contains('credit')) {
                calculateTotals();
            }
        });

        function calculateTotals() {

            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit').forEach(el => {
                totalDebit += parseFloat(el.value) || 0;
            });

            document.querySelectorAll('.credit').forEach(el => {
                totalCredit += parseFloat(el.value) || 0;
            });

            document.getElementById('total-debit').innerText = totalDebit.toLocaleString('id-ID');
            document.getElementById('total-credit').innerText = totalCredit.toLocaleString('id-ID');

            const status = document.getElementById('balance-status');

            if (totalDebit === totalCredit && totalDebit > 0) {
                status.innerHTML = '<span class="badge badge-success">Balance</span>';
            } else {
                status.innerHTML = '<span class="badge badge-danger">Tidak Balance</span>';
            }
        }

        function initSelect2(el) {
            $(el).select2({
                theme: "bootstrap-5",
                width: '100%'
            });
        }

        $('.account-select').each(function() {
            initSelect2(this);
        });

        tableBody.insertAdjacentHTML('beforeend', row);

        initSelect2(
            tableBody.querySelector('tr:last-child .account-select')
        );

        rowIndex++;
    </script>
@stop --}}

@section('js')
    <script>
        let rowIndex = {{ count(old('items', [['']])) }};

        // ================= SELECT2 INIT =================
        function initSelect2(el) {
            $(el).select2({
                theme: "bootstrap-5", // AdminLTE pakai bootstrap4
                width: '100%'
            });
        }

        // init semua select yang sudah ada
        $('.account-select').each(function() {
            initSelect2(this);
        });

        // ================= TAMBAH BARIS =================
        document.getElementById('add-row').addEventListener('click', function() {

            const tableBody = document.querySelector('#journal-table tbody');

            const row = `
        <tr>
            <td>
                <select name="items[${rowIndex}][account_id]"
                        class="form-control account-select"
                        required>

                    <option value="">-- Pilih Akun --</option>

                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach

                </select>
            </td>

            <td>
                <input type="number"
                       name="items[${rowIndex}][debit]"
                       class="form-control debit"
                       step="0.01"
                       value="0">
            </td>

            <td>
                <input type="number"
                       name="items[${rowIndex}][credit]"
                       class="form-control credit"
                       step="0.01"
                       value="0">
            </td>

            <td class="text-center">
                <button type="button"
                        class="btn btn-danger btn-sm remove-row">
                    X
                </button>
            </td>
        </tr>
    `;

            tableBody.insertAdjacentHTML('beforeend', row);

            // 🔥 aktifkan select2 di baris baru
            initSelect2(
                tableBody.querySelector('tr:last-child .account-select')
            );

            rowIndex++;

        });

        // ================= HAPUS BARIS =================
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        // ================= HITUNG TOTAL =================
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('debit') ||
                e.target.classList.contains('credit')) {
                calculateTotals();
            }
        });

        function calculateTotals() {

            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit').forEach(el => {
                totalDebit += parseFloat(el.value) || 0;
            });

            document.querySelectorAll('.credit').forEach(el => {
                totalCredit += parseFloat(el.value) || 0;
            });

            document.getElementById('total-debit').innerText =
                totalDebit.toLocaleString('id-ID');

            document.getElementById('total-credit').innerText =
                totalCredit.toLocaleString('id-ID');

            const status = document.getElementById('balance-status');

            if (totalDebit === totalCredit && totalDebit > 0) {
                status.innerHTML =
                    '<span class="badge badge-success">Balance</span>';
            } else {
                status.innerHTML =
                    '<span class="badge badge-danger">Tidak Balance</span>';
            }
        }

        // hitung saat halaman load (penting setelah validasi)
        calculateTotals();
    </script>
@stop
