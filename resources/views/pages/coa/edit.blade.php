@extends('adminlte::page')

@section('title', 'Edit Account')

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Edit Chart Of Account</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Ada kesalahan pada form
                </div>
            @endif
        </div>

        <form action="{{ route('coa.update', $account->id) }}" method="POST">
            @csrf
            @method('patch')

            <div class="card-body">

                <div class="form-group">
                    <label>Kode</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $account->code) }}"
                        required>
                </div>

                <div class="form-group">
                    <label>Nama Akun</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $account->name) }}"
                        required>
                </div>

                <div class="form-group">

                    <label>Kategori</label>

                    <select name="category" id="category" class="form-control">

                        <option value="asset" {{ $account->category == 'asset' ? 'selected' : '' }}>Asset</option>
                        <option value="liability" {{ $account->category == 'liability' ? 'selected' : '' }}>Liability
                        </option>
                        <option value="equity" {{ $account->category == 'equity' ? 'selected' : '' }}>Equity</option>
                        <option value="revenue" {{ $account->category == 'revenue' ? 'selected' : '' }}>Revenue</option>
                        <option value="expense" {{ $account->category == 'expense' ? 'selected' : '' }}>Expense</option>

                    </select>

                </div>


                <div class="form-group">

                    <label>Akun Induk</label>

                    <select name="parent_id" id="parent_id" class="form-control select2">

                        <option value="">Root Account</option>

                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" data-category="{{ $parent->category }}"
                                {{ $account->parent_id == $parent->id ? 'selected' : '' }}>

                                {{ $parent->code }} - {{ $parent->name }}

                            </option>
                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label>Status</label>

                    <select name="is_active" class="form-control">

                        <option value="1" {{ $account->is_active ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ !$account->is_active ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

            <div class="float-right card-footer">

                <button class="mr-1 btn btn-primary">
                    Update
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


            $('#parent_id').on('select2:open', function() {

                setTimeout(function() {

                    document
                        .querySelector('.select2-container--open .select2-search__field')
                        .focus()

                }, 0)

            });


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
