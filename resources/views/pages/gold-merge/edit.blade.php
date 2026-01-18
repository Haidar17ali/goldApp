@extends('adminlte::page')

@section('title', 'Edit Gold Merge Conversion')

@section('content_header')
    <h1>Edit Gold Merge Conversion</h1>
@stop

@section('content')
    <form action="{{ route('keluar-etalase.update', $conversion->id) }}" method="POST">
        @csrf
        @method('patch')

        <div class="card">
            <div class="card-body">

                {{-- NOTE --}}
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note', $conversion->note) }}</textarea>
                </div>

                <table class="table table-bordered" id="conversionTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th width="120">Qty</th>
                            <th width="180">Stok</th>
                            <th width="50">#</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($conversion->inputs as $i => $input)
                            <tr>
                                <td>
                                    <select name="details[{{ $i }}][product_variant_id]"
                                        class="form-control product select2">
                                        <option value="">-- pilih --</option>
                                        @foreach ($productVariants as $pv)
                                            <option value="{{ $pv->id }}" @selected($pv->id == $input->product_variant_id)>
                                                {{ $pv->product->name }} -
                                                {{ $pv->karat->name ?? '-' }} -
                                                {{ $pv->gram }}g - {{ $pv->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number" name="details[{{ $i }}][qty]" class="form-control qty"
                                        value="{{ $input->qty }}" min="1">
                                </td>

                                <td>
                                    <div class="text-muted stock-info">
                                        Qty tersedia: -
                                    </div>
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row">✖</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm" id="addRow">
                    + Tambah Baris
                </button>

            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">
                    Update
                </button>
            </div>
        </div>
    </form>
@stop
