@extends('adminlte::page')

@section('title', 'Edit Pengelolaan Emas')

@section('content_header')
    <h1>Edit Pengelolaan Emas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="card-body">
            <form action="{{ route('pengelolaan-emas.update', $management->id) }}" method="POST">
                @csrf
                @method('patch')

                <div class="row">
                    {{-- Kolom kiri --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Tanggal</label>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ old('date', $management->date) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Jenis Pengelolaan</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type', $management->type) == $key ? 'selected' : '' }}>
                                        {{ ucfirst($label) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="karat_id">Karat Asal (Stok Customer)</label>
                            <select name="karat_id" id="karat_id" class="form-control select2" required>
                                <option value="">-- Pilih Karat --</option>
                                @foreach ($karats as $karat)
                                    <option value="{{ $karat->id }}"
                                        {{ old('karat_id', $management->karat_id) == $karat->id ? 'selected' : '' }}>
                                        {{ $karat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="karat_info" class="mt-1 text-muted d-block"></small>
                        </div>
                    </div>

                    {{-- Kolom kanan --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gram_out">Gram Keluar (Bahan dari Customer)</label>
                            <input type="number" step="0.01" min="0" name="gram_out" id="gram_out"
                                value="{{ old('gram_out', $management->gram_out) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="gram_in">Gram Masuk (Hasil Pengelolaan)</label>
                            <input type="number" step="0.01" min="0" name="gram_in" id="gram_in"
                                value="{{ old('gram_in', $management->gram_in) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="note">Catatan</label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="Tulis catatan tambahan...">{{ old('note', $management->note) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Perbarui
                    </button>
                    <a href="{{ route('pengelolaan-emas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Ketika user memilih karat, tampilkan info stok customer
            $('#karat_id').on('change', function() {
                let karatId = $(this).val();
                if (!karatId) return;

                $.ajax({
                    url: '/gold-app/stock/info/' + karatId,
                    method: 'GET',
                    success: function(data) {
                        $('#karat_info').html('Tersedia: <strong>' + data.weight +
                            ' gram</strong> dari stok customer');
                    },
                    error: function() {
                        $('#karat_info').html(
                            '<span class="text-danger">Gagal memuat informasi stok.</span>');
                    }
                });
            });

            // Trigger info stok awal saat halaman pertama kali dibuka
            const currentKarat = $('#karat_id').val();
            if (currentKarat) {
                $('#karat_id').trigger('change');
            }
        });
    </script>
@stop
