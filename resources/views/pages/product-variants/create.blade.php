@extends('adminlte::page')

@section('title', 'Buat Product Variant')

@section('content_header')
    <h1>Buat Product Variant</h1>
@stop

@section('content')
    <form action="{{ route('varian-produk.simpan') }}" method="POST" id="formPV">
        @csrf

        <div class="card">
            <div class="card-header">
                <span class="badge badge-primary">Form Input Product Variant</span>
            </div>
            <div class="card-body">

                {{-- Error global --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group row">
                    <label for="product_id" class="col-sm-2 col-form-label">Produk</label>
                    <div class="col-sm-4">
                        <select name="product_id" id="product_id" class="form-control select2">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ strtoupper($product->name) }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text" id="product_id_error"></span>
                    </div>

                    <label for="karat_id" class="col-sm-2 col-form-label">Kadar</label>
                    <div class="col-sm-4">
                        <select name="karat_id" id="karat_id" class="form-control select2">
                            <option value="">-- Pilih Kadar --</option>
                            @foreach ($karats as $karat)
                                <option value="{{ $karat->id }}" {{ old('karat_id') == $karat->id ? 'selected' : '' }}>
                                    {{ strtoupper($karat->name) }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text" id="karat_id_error"></span>
                    </div>
                </div>
      
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Tipe</label>
                    <div class="col-sm-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_new"
                                value="new" {{ old('type', 'new') == 'new' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_new">NEW</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_sepuh"
                                value="sepuh" {{ old('type') == 'sepuh' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_sepuh">SEPUH</label>
                        </div>

                        <span class="text-danger error-text" id="type_error"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="gram" class="col-sm-2 col-form-label">Gram (Berat)</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="gram" name="gram"
                            value="{{ old('gram') }}">
                        <span class="text-danger error-text" id="gram_error"></span>
                    </div>

                    <label for="default_price" class="col-sm-2 col-form-label">Harga Default</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="default_price" name="default_price"
                            value="{{ old('default_price') }}">
                        <span class="text-danger error-text" id="default_price_error"></span>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('varian-produk.index') }}" class="mr-2 btn btn-danger rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih opsi",
                allowClear: true
            });
        });
    </script>
@stop
