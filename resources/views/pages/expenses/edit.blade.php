@extends('adminlte::page')

@section('title', 'Edit Pengeluaran')

@section('content_header')
    <h1>Edit Pengeluaran</h1>
@stop

@section('content')

<div class="card" x-data="expenseForm()">
    <div class="card-body">

        <form method="POST" action="{{ route('pengeluaran-toko.update', $expense->id) }}">
            @csrf
            @method('patch')

            {{-- Tanggal --}}
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ $expense->date }}" required>
            </div>

            <hr>

            {{-- ITEMS --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Nominal</th>
                        <th>Note</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td>
                                <input type="text" class="form-control"
                                       :name="'items['+index+'][item_name]'"
                                       x-model="item.item_name" required>
                            </td>
                            <td>
                                <input type="number" class="form-control"
                                       :name="'items['+index+'][amount]'"
                                       x-model="item.amount" required>
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                       :name="'items['+index+'][note]'"
                                       x-model="item.note">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                        @click="removeItem(index)" x-show="items.length > 1">
                                    X
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <button type="button" class="btn btn-primary mb-3" @click="addItem()">+ Tambah</button>

            <div class="text-right mb-3">
                <h4>Total: Rp <span x-text="format(total())"></span></h4>
            </div>

            <button class="btn btn-success">Update</button>

        </form>

    </div>
</div>

@stop

@section('js')
<script src="https://unpkg.com/alpinejs" defer></script>

<script>
function expenseForm() {
    return {
        items: @json($expense->details->map(function($d){
            return [
                'item_name' => $d->item_name,
                'amount' => $d->amount,
                'note' => $d->note
            ];
        })),

        addItem() {
            this.items.push({ item_name: '', amount: 0, note: '' });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        total() {
            return this.items.reduce((sum, i) => sum + (parseFloat(i.amount) || 0), 0);
        },

        format(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        }
    }
}
</script>
@stop