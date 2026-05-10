@extends('adminlte::page')

@section('title', 'Tambah Pengeluaran')

@section('content_header')
    <h1>Tambah Pengeluaran</h1>
@stop

@section('content')

<div class="card" x-data="expenseForm()" x-init="init()">
    <div class="card-body">

        <form method="POST" action="{{ route('pengeluaran-toko.simpan') }}">
            @csrf

            {{-- Tanggal --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <hr>

            {{-- TABLE ITEM --}}
            <div class="table-responsive">
                <table class="table table-bordered"  x-ref="table">
                    <thead>
                        <tr>
                            <th>Nama Pengeluaran</th>
                            <th width="200">Nominal</th>
                            <th width="250">Keterangan</th>
                            <th width="250">Tipe Pembayaran</th>
                            <th width="50">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td>
                                    <input type="text"
                                           class="form-control"
                                           :name="'items['+index+'][item_name]'"
                                           x-model="item.item_name"
                                           required>
                                </td>

                                <td>
                                    <input type="number"
                                           class="form-control"
                                           :name="'items['+index+'][amount]'"
                                           x-model="item.amount"
                                           required>
                                </td>

                                <td>
                                    <input type="text"
                                           class="form-control"
                                           :name="'items['+index+'][note]'"
                                           x-model="item.note">
                                </td>
                                <td>
                                    <select class="form-control select-payment"
                                            :name="'items['+index+'][payment_type]'"
                                            x-model="item.payment_type"
                                            required>

                                        <option value="">-- Pilih Pembayaran --</option>

                                        {{-- Tunai --}}
                                        <option value="cash">Tunai</option>

                                        {{-- Rekening Bank --}}
                                        @foreach($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}">
                                                {{ $bank->account_holder }} - {{ $bank->account_number }}
                                            </option>
                                        @endforeach

                                    </select>
                                </td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>

                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- BUTTON TAMBAH --}}
            <div class="mb-3">
                <button type="button" class="btn btn-primary" @click="addItem()">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            {{-- TOTAL (OPSIONAL tapi bagus banget) --}}
            <div class="text-right mb-3">
                <h4>
                    Total: Rp 
                    <span x-text="formatRupiah(total())"></span>
                </h4>
            </div>

            {{-- SUBMIT --}}
            <div class="text-right">
                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>

        </form>

    </div>
</div>

@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- SELECT2 BOOTSTRAP 5 THEME --}}
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@endsection

@section('js')

<script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// function expenseForm() {
//     return {
//         items: [
//             { item_name: '', amount: 0, note: '', payment_type: '' }
//         ],

//         addItem() {
//             this.items.push({ item_name: '', amount: 0, note: '', payment_type: '' });
//         },

//         removeItem(index) {
//             this.items.splice(index, 1);
//         },

//         total() {
//             return this.items.reduce((sum, item) => {
//                 return sum + (parseFloat(item.amount) || 0);
//             }, 0);
//         },

//         formatRupiah(number) {
//             return new Intl.NumberFormat('id-ID').format(number);
//         }
//     }
// }

</script>
<script>
function expenseForm() {
    return {
        items: [
            { item_name: '', amount: 0, note: '', payment_type: '' }
        ],

        init() {
            this.$nextTick(() => {
                this.initSelect2();
            });
        },

        addItem() {
            this.items.push({
                item_name: '',
                amount: 0,
                note: '',
                payment_type: ''
            });

            this.$nextTick(() => {
                this.initSelect2();
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);

            this.$nextTick(() => {
                this.initSelect2();
            });
        },

        initSelect2() {
            $('.select-payment').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Pembayaran --'
            });

            // AUTO FOCUS SEARCH
            $('.select-payment').on('select2:open', function () {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            });
        },

        total() {
            return this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.amount) || 0);
            }, 0);
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }
}
</script>

@stop