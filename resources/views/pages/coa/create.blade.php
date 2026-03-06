@extends('adminlte::page')

@section('title', 'Create Account')

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Create Chart Of Account</h3>

            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <form action="{{ route('coa.simpan') }}" method="POST">
            @csrf
            @method('post')

            <div class="card-body">

                <div class="form-group">
                    <label>Kode</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Nama Akun</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" id="category" class="form-control">

                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>

                    </select>
                </div>

                <div class="form-group">

                    <label>Akun Induk</label>

                    <select name="parent_id" id="parent_id" class="form-control select2">

                        <option value="">Root Account</option>

                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" data-category="{{ $parent->category }}">
                                {{ str_repeat('— ', $parent->level ?? 0) }}
                                {{ $parent->code }} - {{ $parent->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select name="is_active" class="form-control">

                        <option value="1">Active</option>
                        <option value="0">Inactive</option>

                    </select>

                </div>

            </div>

            <div class="float-right card-footer">

                <button class="mr-1 btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('coa.index') }}" class="btn btn-danger">
                    Kembali
                </a>

            </div>

        </form>

    </div>

@stop

@section('js')
    <script>
        $(document).ready(function() {

            $('#parent_id').select2({
                width: '100%',
                theme: "bootstrap-5",
                placeholder: "Pilih akun induk",
                allowClear: true
            });

            // auto focus search
            $('#parent_id').on('select2:open', function() {
                setTimeout(function() {
                    document.querySelector('.select2-container--open .select2-search__field')
                        .focus();
                }, 0);
            });


            // auto category mengikuti parent
            $('#parent_id').on('change', function() {

                let selected = $(this).find(':selected')
                let category = selected.data('category')

                if (category) {

                    $('#category').val(category)

                }

            });

        });
    </script>
@stop
