<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Shift</th>
            <th scope="col">Jenis Kayu</th>
            <th scope="col">Tally</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ date('d-m-Y', strtotime($item->date)) }}</td>
                    <td>{{ $item->shift }}</td>
                    <td>{{ $item->wood_type }}</td>
                    <td>{{ $item->tally != null ? $item->tally->alias_name : '-' }}</td>
                    <td>
                        <a href="{{ route('rotari.ubah', ['id' => $item->id, 'type' => $item->type]) }}"
                            class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('rotari.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="7" class="text-center"><b>Data {{ $type }} tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
