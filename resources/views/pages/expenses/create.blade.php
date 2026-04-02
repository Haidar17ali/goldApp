@extends('adminlte::page')

@section('title', 'Tambah Pengeluaran')

@section('content_header')
    <h1>Tambah Pengeluaran</h1>
@stop

@section('content')

<div class="card" x-data="expenseForm()">
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Pengeluaran</th>
                            <th width="200">Nominal</th>
                            <th width="250">Keterangan</th>
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

@section('js')

<script src="https://unpkg.com/alpinejs" defer></script>

<script>
function expenseForm() {
    return {
        items: [
            { item_name: '', amount: 0, note: '' }
        ],

        addItem() {
            this.items.push({ item_name: '', amount: 0, note: '' });
        },

        removeItem(index) {
            this.items.splice(index, 1);
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