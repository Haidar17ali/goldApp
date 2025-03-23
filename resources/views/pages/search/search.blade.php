<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Supplier</th>
            <th scope="col">Nominal</th>
            <th scope="col">Tipe</th>
            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ date('d-m-Y', strtotime($item->date)) }}</td>
                    <td>{{ optional($item->supplier)->name ?? 'Supplier tidak ditemukan' }}</td>
                    <td>Rp.{{ number_format($item->nominal) }}</td>
                    <td style="color:{{ $item->type == 'In' ? 'green' : 'red' }}">
                        <i class="fas fa-arrow-{{ $item->type == 'In' ? 'up' : 'down' }}"></i>
                        {{ $item->type }}
                    </td>
                    <td>
                        <span
                            class="badge badge-{{ $item->status == 'Pending' ? 'warning' : ($item->status == 'Gagal' ? 'danger' : 'success') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>
                        @if ($item->status == 'Pending')
                            <a href="{{ route('utility.activation-dp', ['modelType' => 'Down_payment', 'id' => $item->id, 'status' => 'Menunggu Pembayaran']) }}"
                                class="badge badge-success">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="{{ route('utility.activation-dp', ['modelType' => 'down_payment', 'id' => $item->id, 'status' => 'Gagal']) }}"
                                class="badge badge-danger">
                                <i class="fas fa-times"></i>
                            </a>
                            ||
                            <a href="{{ route('down-payment.ubah', $item->id) }}" class="badge badge-success">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('down-payment.hapus', $item->id) }}" class="d-inline"
                                method="post">
                                @csrf
                                @method('DELETE')
                                <a href="#" data-id="{{ $item->id }}"
                                    class="badge badge-pill badge-delete badge-danger d-inline">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center"><b>Data down-payment tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
