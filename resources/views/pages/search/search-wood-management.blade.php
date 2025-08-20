<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">No Kitir</th>
            <th scope="col">Grade</th>
            <th scope="col">Dari</th>
            <th scope="col">Ke</th>
            <th scope="col">Tally</th>
            <th scope="col">Kubikasi Asal</th>
            <th scope="col">Kubikasi</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                @php
                    $totalKubikasi = 0;
                    $sourceTotalKubikasi = 0;
                    if (count($item->details)) {
                        foreach ($item->details as $detail) {
                            $totalKubikasi += kubikasi(
                                $detail->conversion_diameter,
                                $item->to,
                                $detail->conversion_qty,
                            );
                            $sourceTotalKubikasi += kubikasi(
                                $detail->source_diameter,
                                $item->from,
                                $detail->source_qty,
                            );
                        }
                    }
                @endphp
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ date('d-m-Y', strtotime($item->date)) }}</td>
                    <td>{{ $item->no_kitir }}</td>
                    <td>{{ $item->grade }}</td>
                    <td>{{ $item->from }}</td>
                    <td>{{ $item->to }}</td>
                    <td>{{ $item->tally != null ? $item->tally->alias_name : '-' }}</td>
                    <td>{{ $sourceTotalKubikasi }}</td>
                    <td>{{ $totalKubikasi }}</td>
                    <td>
                        <a href="{{ route('pengelolaan-kayu.ubah', ['id' => $item->id, 'type' => $item->type]) }}"
                            class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('pengelolaan-kayu.hapus', $item->id) }}" class="d-inline"
                            method="post">
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
                <td colspan="8" class="text-center"><b>Data pengelolaan kayu {{ $type }} tidak
                        ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
