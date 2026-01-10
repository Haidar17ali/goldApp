@extends('adminlte::page')

@section('title', 'Edit Product Variant')

@section('content_header')
    <h1>Edit Product Variant</h1>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('varian-produk.update', $productVariant->id) }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="card">
        <div class="card-body">

            {{-- PRODUCT --}}
            <div class="form-group row">
                <label for="product_id" class="col-sm-2 col-form-label">
                    Product <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <select name="product_id" id="product_id" class="form-control select2">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ old('product_id', $productVariant->product_id) == $product->id ? 'selected' : '' }}>
                                {{ strtoupper($product->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- SKU (READ ONLY) --}}
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">SKU</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control"
                        value="{{ $productVariant->sku }}" disabled>
                </div>
            </div>

            {{-- TYPE --}}
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">
                    Type <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4 d-flex align-items-center">
                    <div class="form-check mr-4">
                        <input class="form-check-input" type="radio" name="type" id="type_new"
                            value="new"
                            {{ old('type', $productVariant->type) == 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_new">NEW</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="type_sepuh"
                            value="sepuh"
                            {{ old('type', $productVariant->type) == 'sepuh' ? 'checked' : '' }}>
                        <label class="form-check-label" for="type_sepuh">SEPUH</label>
                    </div>
                </div>
            </div>

            {{-- GRAM --}}
            <div class="form-group row">
                <label for="gram" class="col-sm-2 col-form-label">
                    Gram <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <input type="number" step="0.01" min="0"
                        class="form-control"
                        id="gram" name="gram"
                        value="{{ old('gram', $productVariant->gram) }}"
                        placeholder="Contoh: 1.25">
                </div>
            </div>

            {{-- DEFAULT PRICE --}}
            <div class="form-group row">
                <label for="default_price" class="col-sm-2 col-form-label">
                    Harga Default <span class="text-danger">*</span>
                </label>
                <div class="col-sm-4">
                    <input type="number" min="0"
                        class="form-control"
                        id="default_price" name="default_price"
                        value="{{ old('default_price', $productVariant->default_price) }}"
                        placeholder="Contoh: 950000">
                </div>
            </div>

            {{-- NOTE --}}
            <div class="form-group row">
                <label for="note" class="col-sm-2 col-form-label">Catatan</label>
                <div class="col-sm-6">
                    <textarea name="note" id="note" rows="3"
                        class="form-control"
                        placeholder="Catatan tambahan (opsional)">{{ old('note', $productVariant->note) }}</textarea>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer text-right">
            <a href="{{ route('varian-produk.index') }}" class="btn btn-secondary">
                Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </div>
    </div>
</form>

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: '-- Pilih Produk --',
            allowClear: true
        });
    });
</script>
@stop
