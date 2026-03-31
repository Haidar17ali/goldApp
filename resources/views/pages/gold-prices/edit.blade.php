@extends('adminlte::page')

@section('title', 'Edit Jurnal')

@section('content_header')
    <h1>Edit Jurnal Umum</h1>
@stop

@section('content')

    <form action="{{ route('jurnal.update', $journal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Form Edit Jurnal</h3>

                @if ($errors->any())
                    <div class="mt-2 alert alert-danger">
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
                        <input type="date" name="date" class="form-control" value="{{ old('date', $journal->date) }}"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label>Deskripsi</label>
                        <input type="text" name="description" class="form-control"
                            value="{{ old('description', $journal->description) }}">
                    </div>

                </div>

                {{-- DETAIL --}}
                @php
                    $oldItems = old(
                        'items',
                        $journal->items
                            ->map(function ($item) {
                                return [
                                    'account_id' => $item->chart_of_account_id,
                                    'debit' => $item->debit,
                                    'credit' => $item->credit,
                                ];
                            })
                            ->toArray(),
                    );
                @endphp

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

                            @foreach ($oldItems as $i => $item)
                                <tr>

                                    <td>
                                        <select name="items[{{ $i }}][account_id]"
                                            class="form-control account-select" required>

                                            <option value="">-- Pilih Akun --</option>

                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}"
                                                    {{ $item['account_id'] == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->code }} - {{ $acc->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" name="items[{{ $i }}][debit]"
                                            class="form-control debit" step="0.01" value="{{ $item['debit'] }}">
                                    </td>

                                    <td>
                                        <input type="number" name="items[{{ $i }}][credit]"
                                            class="form-control credit" step="0.01" value="{{ $item['credit'] }}">
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
                    Update Jurnal
                </button>
            </div>

        </div>

    </form>

@stop


@section('js')
    <script>
        let rowIndex = {{ count($oldItems) }};

        function initSelect2(el) {
            $(el).select2({
                theme: "bootstrap-5",
                width: '100%'
            });
        }

        $('.account-select').each(function() {
            initSelect2(this);
        });

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

            initSelect2(
                tableBody.querySelector('tr:last-child .account-select')
            );

            rowIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

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

        calculateTotals();
    </script>
@stop
