<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Kode</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Aksi</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr style="text-transform: uppercase">
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->code }}</td>
                    <td>{{ date('d-m-y', strtotime($item->date)) }}</td>
                    <td>{{ $item->tailor_name }}</td>
                    <td>
                        <a href="#" class="badge badge-primary"><i class="fas fa-eye"></i> Detail</a>
                        <a href="{{ route('cutting.ubah', $item->id) }}" class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('cutting.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="7" class="text-center"><b>Data Potongan tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
