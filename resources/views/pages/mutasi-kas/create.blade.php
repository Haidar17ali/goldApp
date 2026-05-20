@extends('adminlte::page')

@section('title', 'Tambah Mutasi Kas')

@section('content_header')
    <h1>Tambah Mutasi Kas</h1>
@stop

@section('content')

    {{-- ERROR VALIDATION --}}
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
        <form action="{{ route('mutasi-kas.simpan') }}" method="POST">
            @csrf

            <div class="card-body">

                <div class="form-group">
                    <label>Tanggal</label>

                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                        value="{{ old('date', date('Y-m-d')) }}" required>

                    @error('date')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Dari</label>

                    <select name="from_bank_account_id" id="from_bank_account_id"
                        class="form-control select2 @error('from_bank_account_id') is-invalid @enderror">

                        <option value="">Kas Tunai</option>

                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ old('from_bank_account_id') == $bank->id ? 'selected' : '' }}>

                                {{ $bank->account_holder }} - {{ $bank->account_number }}

                            </option>
                        @endforeach
                    </select>

                    @error('from_bank_account_id')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Ke</label>

                    <select name="to_bank_account_id" id="to_bank_account_id"
                        class="form-control select2 @error('to_bank_account_id') is-invalid @enderror">

                        <option value="">Kas Tunai</option>

                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"
                                {{ old('to_bank_account_id') == $bank->id ? 'selected' : '' }}>

                                {{ $bank->account_holder }} - {{ $bank->account_number }}

                            </option>
                        @endforeach
                    </select>

                    @error('to_bank_account_id')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Jumlah</label>

                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount') }}" required>

                    @error('amount')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Keterangan</label>

                    <textarea name="note" class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>

                    @error('note')
                        <span class="invalid-feedback d-block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            <div class="card-footer">
                <button class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('mutasi-kas.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </form>
    </div>

@stop

@section('plugins.Select2', true)

@section('js')

    <script>
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%',
                placeholder: '-- Pilih --',
                theme: 'bootstrap-5',
                allowClear: true
            });

            // autofocus search ketika select2 dibuka
            $(document).on('select2:open', () => {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            });

        });
    </script>

@stop
