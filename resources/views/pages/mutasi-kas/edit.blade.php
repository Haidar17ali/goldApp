@extends('adminlte::page')

@section('title', 'Edit Mutasi Kas')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Edit Mutasi Kas</h1>
@stop

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">

        <form action="{{ route('mutasi-kas.update', $cashMutation->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="card-body">

                <div class="form-group">
                    <label>Tanggal</label>

                    <input type="date" name="date" class="form-control" value="{{ old('date', $cashMutation->date) }}"
                        required>
                </div>

                <div class="form-group">
                    <label>Dari</label>

                    <select name="from_bank_account_id" class="form-control select2">

                        <option value="">Kas Tunai</option>

                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ old('from_bank_account_id', $cashMutation->from_bank_account_id) == $bank->id ? 'selected' : '' }}>

                                {{ $bank->account_holder }}
                                - {{ $bank->account_number }}

                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Ke</label>

                    <select name="to_bank_account_id" class="form-control select2">

                        <option value="">Kas Tunai</option>

                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ old('to_bank_account_id', $cashMutation->to_bank_account_id) == $bank->id ? 'selected' : '' }}>

                                {{ $bank->account_holder }}
                                - {{ $bank->account_number }}

                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>

                    <input type="number" name="amount" class="form-control"
                        value="{{ old('amount', $cashMutation->amount) }}" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>

                    <textarea name="note" class="form-control">{{ old('note', $cashMutation->note) }}</textarea>
                </div>

            </div>

            <div class="card-footer">
                <button class="btn btn-primary">
                    Update
                </button>

                <a href="{{ route('mutasi-kas.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

        </form>

    </div>

@stop

@section('js')

    <script>
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%',
                placeholder: '-- Pilih --',
                theme: 'bootstrap-5',
                allowClear: true
            });

            $(document).on('select2:open', () => {
                document.querySelector(
                    '.select2-container--open .select2-search__field'
                ).focus();
            });

        });
    </script>

@stop
