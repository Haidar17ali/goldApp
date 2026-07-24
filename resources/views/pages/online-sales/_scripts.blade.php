<script>
    $(function() {

        /**
         * ============================================================
         * Tooltip
         * ============================================================
         */
        $('[data-toggle="tooltip"]').tooltip();

        /**
         * ============================================================
         * Auto Submit Filter
         * ============================================================
         */
        $('#order_status, #payment_status').on('change', function() {
            $(this).closest('form').submit();
        });

        /**
         * ============================================================
         * Enter Search
         * ============================================================
         */
        $('input[name="search"]').on('keypress', function(e) {

            if (e.which === 13) {
                $(this).closest('form').submit();
            }

        });

        /**
         * ============================================================
         * Reset Filter
         * ============================================================
         */
        $('#btn-reset-filter').on('click', function() {

            window.location.href = "{{ route('penjualan.online.index', $marketplace->code) }}";

        });

        /**
         * ============================================================
         * Sync Marketplace
         * ============================================================
         */

        $('#btn-sync').on('click', function(e) {

            e.preventDefault();

            Swal.fire({

                icon: 'info',

                title: 'Coming Soon',

                text: 'Sinkronisasi Marketplace sedang dalam tahap pengembangan.',

                confirmButtonText: 'OK'

            });

        });

        /**
         * ============================================================
         * Delete
         * ============================================================
         */

        // $(document).on('click', '.btn-delete', function(e) {

        //     e.preventDefault();

        //     let form = $(this).closest('form');

        //     Swal.fire({

        //         title: 'Hapus Data?',

        //         text: 'Data yang dihapus tidak dapat dikembalikan.',

        //         icon: 'warning',

        //         showCancelButton: true,

        //         confirmButtonColor: '#d33',

        //         cancelButtonColor: '#6c757d',

        //         confirmButtonText: 'Ya, Hapus',

        //         cancelButtonText: 'Batal'

        //     }).then((result) => {

        //         if (result.isConfirmed) {

        //             form.submit();

        //         }

        //     });

        // });

    });
</script>



<script script>
    $(document).on('click', '.btn-delete', function(e) {

        e.preventDefault();

        let url = $(this).data('url');

        Swal.fire({

            title: 'Hapus Penjualan?',

            text: 'Data penjualan, jurnal dan stok akan dikembalikan.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#d33',

            cancelButtonColor: '#6c757d',

            confirmButtonText: 'Ya, Hapus',

            cancelButtonText: 'Batal'

        }).then((result) => {

            if (!result.value) return;

            $.ajax({

                url: url,

                type: 'DELETE',

                data: {
                    _token: "{{ csrf_token() }}"
                },

                beforeSend() {

                    Swal.fire({

                        title: 'Menghapus...',

                        text: 'Mohon tunggu',

                        allowOutsideClick: false,

                        didOpen: () => {
                            Swal.showLoading();
                        }

                    });

                },

                success(res) {

                    Swal.fire({

                        icon: 'success',

                        title: 'Berhasil',

                        text: res.message

                    }).then(() => {

                        location.reload();

                    });

                },

                error(xhr) {

                    Swal.fire({

                        icon: 'error',

                        title: 'Gagal',

                        text: xhr.responseJSON?.message ??
                            'Terjadi kesalahan.'

                    });

                }

            });

        });

    });

    // check data

    function updateSelection() {

        let total = $('.transaction-check:checked').length;

        $('#selectedCount').text(total);

        if (total > 0) {

            $('#btnKasOnline').removeClass('d-none');

        } else {

            $('#btnKasOnline').addClass('d-none');

        }
    }

    $('#checkAll').change(function() {

        $('.transaction-check').prop(
            'checked',
            $(this).is(':checked')
        );

        updateSelection();

    });

    $(document).on('change', '.transaction-check', function() {

        updateSelection();

    });

    $('#btnKasOnline').click(function() {

        let ids = [];

        $('.transaction-check:checked').each(function() {

            ids.push($(this).val());

        });

        if (ids.length == 0) {
            return;
        }

        $.ajax({

            url: "{{ route('penjualan.online.ubah-status') }}",
            type: "POST",

            data: {
                ids: ids,
                _token: "{{ csrf_token() }}"
            },

            success: function(res) {

                alert(res.message);

                location.reload();

            }

        });

        // nanti redirect atau ajax
    });
</script>
