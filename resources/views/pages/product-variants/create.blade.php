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

                    <label for="color_id" class="col-sm-2 col-form-label">Warna</label>
                    <div class="col-sm-4">
                        <select name="color_id" id="color_id" class="form-control select2">
                            <option value="">-- Pilih Warna --</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>
                                    {{ strtoupper($color->name) }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text" id="color_id_error"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="size_id" class="col-sm-2 col-form-label">Ukuran</label>
                    <div class="col-sm-4">
                        <select name="size_id" id="size_id" class="form-control select2">
                            <option value="">-- Pilih Ukuran --</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                    {{ strtoupper($size->name) }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text" id="size_id_error"></span>
                    </div>

                    <label for="default_price" class="col-sm-2 col-form-label">Harga Default</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="default_price" name="default_price"
                            value="{{ old('default_price') }}">
                        <span class="text-danger error-text" id="default_price_error"></span>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('varian-produk.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
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
