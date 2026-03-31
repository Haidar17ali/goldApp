@extends('adminlte::page')

@section('title', 'Tambah Harga Emas')

@section('content_header')
    <h1>Tambah Harga Emas</h1>
@stop

@section('content')

    <form action="{{ route('set-harga.simpan') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">

                {{-- Tanggal --}}
                <div class="row">
                    <div class="col-md-6">
                        <label>Tanggal Aktif</label>
                        <input type="date" name="active_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <hr>

                {{-- Harga per Kadar --}}
                <h5>Harga per Kadar</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kadar</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($karats as $karat)
                                <tr>
                                    <td>
                                        {{ $karat->name }}
                                        <input type="hidden" name="details[{{ $loop->index }}][karat_id]"
                                            value="{{ $karat->id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $loop->index }}][price]"
                                            class="form-control" placeholder="Masukkan harga" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="text-right card-footer">
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </div>

    </form>

@stop
