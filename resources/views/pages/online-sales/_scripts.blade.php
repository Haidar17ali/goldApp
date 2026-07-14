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

        $(document).on('click', '.btn-delete', function(e) {

            e.preventDefault();

            let form = $(this).closest('form');

            Swal.fire({

                title: 'Hapus Data?',

                text: 'Data yang dihapus tidak dapat dikembalikan.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#d33',

                cancelButtonColor: '#6c757d',

                confirmButtonText: 'Ya, Hapus',

                cancelButtonText: 'Batal'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });

    });
</script>
