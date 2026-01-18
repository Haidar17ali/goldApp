<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Berat</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr style="text-transform: uppercase">
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                    <td>
                        {{ $item->inputs->sum(function ($row) {
                            return ($row->productVariant->gram ?? 0) * $row->qty;
                        }) }}
                        g
                    </td>
                    <td>{{ $item->note }}</td>
                    <td>
                        {{-- <a href="{{ route('penjualan.cetak', $item->id) }}" target="_blank" class="badge badge-success">
                            <i class="fas fa-print"></i>
                        </a> --}}
                        <a href="{{ route('keluar-etalase.detail', $item->id) }}" class="badge badge-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('keluar-etalase.ubah', $item->id) }}" class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('keluar-etalase.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="8" class="text-center"><b>Data Keluar Etalasa tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
