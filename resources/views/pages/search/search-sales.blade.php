<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">No Invoice</th>
            <th scope="col">Tanggal Transaksi</th>
            <th scope="col">Nama Customer</th>
            <th scope="col">Total Berat</th>
            <th scope="col">Total Transaksi</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr style="text-transform: uppercase">
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->invoice_number }}</td>
                    <td>{{ $item->transaction_date }}</td>
                    <td>{{ $item->customer?->name ?? 'null' }}</td>
                    <td>{{ $item->details->sum(function ($detail) {
                        return $detail->productVariant->gram ?? 0;
                    }) }}g
                    </td>
                    <td>{{ money_format($item->total) }}</td>
                    <td>{{ $item->note }}</td>
                    <td>
                        <a href="#" class="badge badge-info btn-detail" data-id="{{ $item->id }}">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('penjualan.cetak', $item->id) }}" target="_blank" class="badge badge-success">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="{{ route('penjualan.ubah', ['type' => $type, 'id' => $item->id]) }}"
                            class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('penjualan.hapus', ['type' => $type, 'id' => $item->id]) }}"
                            class="d-inline" method="post">
                            @csrf
                            @method('DELETE')
                            <a href="#" data-id="{{ $item->id }}"
                                class="badge badge-pill badge-delete badge-danger d-inline">
                                <i class="fas fa-trash"></i>
                            </a>
                        </form>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" class="text-center"><b>Data penjualan tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>


{{-- modal --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="detail-content" class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </div>
            </div>

        </div>
    </div>
</div>
