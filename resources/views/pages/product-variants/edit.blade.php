@extends('adminlte::page')

@section('title', 'Edit Product Variant')

@section('content_header')
    <h1>Edit Product Variant</h1>
@stop

@section('content')
    <form action="{{ route('varian-produk.update', $productVariant->id) }}" method="POST" id="formPV">
        @csrf
        @method('patch')

        <div class="card">
            <div class="card-header">
                <span class="badge badge-primary">Form Edit Product Variant</span>
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
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ $productVariant->product_id == $product->id ? 'selected' : '' }}>
                                    {{ strtoupper($product->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label for="color_id" class="col-sm-2 col-form-label">Warna</label>
                    <div class="col-sm-4">
                        <select name="color_id" id="color_id" class="form-control select2">
                            <option value="">-- Pilih Warna --</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}"
                                    {{ $productVariant->color_id == $color->id ? 'selected' : '' }}>
                                    {{ strtoupper($color->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="size_id" class="col-sm-2 col-form-label">Ukuran</label>
                    <div class="col-sm-4">
                        <select name="size_id" id="size_id" class="form-control select2">
                            <option value="">-- Pilih Ukuran --</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size->id }}"
                                    {{ $productVariant->size_id == $size->id ? 'selected' : '' }}>
                                    {{ strtoupper($size->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label for="default_price" class="col-sm-2 col-form-label">Harga Default</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="default_price" name="default_price"
                            value="{{ old('default_price', $productVariant->default_price) }}">
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('varian-produk.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Update Data</button>
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
