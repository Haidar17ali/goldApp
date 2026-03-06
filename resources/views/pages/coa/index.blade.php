@extends('adminlte::page')

@section('title', 'Chart Of Accounts')

@section('content')

    <div class="card">

        <div class="card-header">

            <h3 class="float-left card-title">Chart Of Accounts</h3>

            <a href="{{ route('coa.buat') }}" class="float-right btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Account
            </a>

        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="bg-dark">
                    <tr>
                        <th width="50"></th>
                        <th width="150">Kode</th>
                        <th>Nama Akun</th>
                        <th width="150">Kategori</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @include('pages.coa.row', [
                        'accounts' => $accounts,
                        'level' => 0,
                        'parent' => 0,
                    ])

                </tbody>

            </table>

        </div>

    </div>

@stop


@section('Sweetalert2')
@section('js')

    <script>
        $('.form-delete').submit(function(e) {

            e.preventDefault()

            let form = this
            let hasChild = $(this).data('has-child')

            if (hasChild == 1) {

                Swal.fire({
                    title: 'Hapus akun induk?',
                    text: "Menghapus akun ini akan menghapus semua akun turunannya!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus semua!'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit()
                    }

                })

            } else {

                Swal.fire({
                    title: 'Hapus akun?',
                    text: "Apakah anda yakin akan menghapus data ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit()
                    }

                })

            }

        })

        $(document).on('click', '.toggle', function() {

            let row = $(this).closest('tr')
            let id = row.data('id')
            let button = $(this)

            let children = $('tr[data-parent="' + id + '"]')

            if (button.text() == '+') {

                children.show()
                button.text('-')

            } else {

                hideChildren(id)
                button.text('+')

            }

        })


        function hideChildren(parentId) {

            let children = $('tr[data-parent="' + parentId + '"]')

            children.each(function() {

                let childId = $(this).data('id')

                $(this).hide()

                hideChildren(childId)

                $(this).find('.toggle').text('+')

            })

        }


        // hide semua child saat load
        $('tr[data-parent!=""]').each(function() {

            if ($(this).data('parent') != 0) {
                $(this).hide()
            }

        })
    </script>

@stop
