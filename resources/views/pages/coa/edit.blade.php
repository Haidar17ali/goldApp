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
                                data-branch="{{ $parent->branch_id }}"
                                {{ $account->parent_id == $parent->id ? 'selected' : '' }}>

                                {{ $parent->code }} - {{ $parent->name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Cabang</label>

                    <select name="branch_id" id="branch_id" class="form-control select2">

                        <option value="">Global</option>

                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', $account->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->code }} - {{ $branch->name }}
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
                theme: 'bootstrap-5',
                placeholder: 'Pilih akun induk',
                allowClear: true
            });

            $('#branch_id').select2({
                width: '100%',
                theme: 'bootstrap-5',
                placeholder: 'Pilih Cabang',
                allowClear: true
            });

            $('#parent_id').on('select2:open', function() {
                setTimeout(function() {
                    document.querySelector(
                        '.select2-container--open .select2-search__field'
                    ).focus();
                }, 0);
            });

            function toggleBranch() {

                let selected = $('#parent_id').find(':selected');
                let branch = selected.data('branch');

                // Root Account
                if ($('#parent_id').val() == '') {

                    $('#branch_id')
                        .val('')
                        .trigger('change')
                        .prop('disabled', true);

                    return;
                }

                // Parent milik cabang tertentu
                if (branch) {

                    $('#branch_id')
                        .val(branch)
                        .trigger('change')
                        .prop('disabled', true);

                }
                // Parent global
                else {

                    $('#branch_id')
                        .prop('disabled', false);

                }
            }

            toggleBranch();

            $('#parent_id').on('change', function() {

                let selected = $(this).find(':selected');

                let category = selected.data('category');

                if (category) {
                    $('#category').val(category);
                }

                toggleBranch();

            });

            $('form').on('submit', function() {
                $('#branch_id').prop('disabled', false);
            });

        });
    </script>

@stop
