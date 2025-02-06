@extends('adminlte::page')

@section('title', 'Ubah Posisi')

@section('content_header')
    <h1>Ubah Posisi</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="badge badge-primary float-right">Ubah Posisi</div>
        </div>
        <div class="card-body">
            <form action="{{ route('bagian.update', $position->id) }}" method="POST">
                @csrf
                @method('patch')
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $position->name) }}">
                        @error('name')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Tipe</label>
                    <div class="col-sm-10">
                        <select name="type" class="form-control @error('type') is-invalid @enderror" id="type">
                            <option value="null">Silahkan Pilih Tipe</option>
                            @foreach ($types as $type)
                                <option {{ $position->type == $type ? 'selected' : '' }} value="{{ $type }}">
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="parent" class="col-sm-2 col-form-label">Induk</label>
                    <div class="col-sm-10">
                        <select name="parent" class="form-control @error('parent') is-invalid @enderror" id="parent"
                            disabled>
                            <option selected value="null">Silahkan Pilih Induk Bagian</option>
                        </select>
                        @error('parent')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="float-right mt-3">
                    <a href="{{ route('bagian.index') }}" class="btn btn-danger rounded-pill mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
    <script>
        // select2
        $(document).ready(function() {
            $('#type').select2({
                theme: "bootstrap4",
            });
            $('#parent').select2({
                theme: "bootstrap4",
            });

            var parent = {{ $position->parent }}

            if ($("#type").val() !== null) {
                $.ajax({
                    url: "{{ route('bagian.tipe') }}",
                    type: "GET",
                    data: {
                        type: $("#type").val()
                    },
                    success: function(response) {
                        $('#parent').empty().append(
                            '<option value="">Silahkan Pilih Induk Bagian</option>'
                        );
                        $.each(response, function(key, value) {
                            let parentBadge = value.parent ?
                                `<span class="badge badge-primary ml-2">[${value.parent.name}]</span>` :
                                "";

                            $('#parent').append(
                                `<option ${value.id == parent? 'selected' : ''} value="${value.id}">${value.name} ${parentBadge}</option>`
                            );
                        });
                        $('#parent').prop('disabled', false);
                    }
                })
            }

            // ajax data select parent
            $('#type').on("select2:select", function(e) {
                let typeValue = $(this).val();


                if (typeValue == "Departemen" || typeValue == "Bagian") {
                    $.ajax({
                        url: "{{ route('bagian.tipe') }}",
                        type: "GET",
                        data: {
                            type: typeValue
                        },
                        success: function(response) {
                            $('#parent').empty().append(
                                '<option value="">Silahkan Pilih Induk Bagian</option>'
                            );
                            $.each(response, function(key, value) {
                                let parentBadge = value.parent ?
                                    `<span class="badge badge-primary ml-2">[${value.parent.name}]</span>` :
                                    "";

                                $('#parent').append(
                                    `<option value="${value.id}">${value.name} ${parentBadge}</option>`
                                );
                            });
                            $('#parent').prop('disabled', false);
                        }
                    })
                } else {
                    $('#parent').prop('disabled', true);

                }

            })
        });
    </script>
@stop
