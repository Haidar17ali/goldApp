<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Kode</th>
            <th scope="col">Nama</th>
            <th scope="col">Alamat</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr style="text-transform: uppercase">
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->address }}</td>
                    <td>
                        <a href="{{ route('cabang.ubah', $item->id) }}" class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('cabang.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="7" class="text-center"><b>Data cabang tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
