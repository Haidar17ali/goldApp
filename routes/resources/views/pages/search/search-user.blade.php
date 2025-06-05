<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->getRoleNames() }}</td>
                    <td>
                        <span class="badge badge-{{ $item->is_active == true ? 'success' : 'danger' }}">
                            {{ $item->is_active == 1 ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('pengguna.ubah', $item->id) }}" class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('pengguna.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="7" class="text-center"><b>Data down-payment tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
