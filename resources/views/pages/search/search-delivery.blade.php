<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Nama Pengirim</th>
            <th scope="col">Tanggal Datang</th>
            <th scope="col">Pembuat</th>
            <th scope="col">Pengedit</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr style="text-transform: uppercase">
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ date('d-m-y H:i:s', strtotime($item->date)) }}</td>
                    <td>{{ $item->sender }}</td>
                    <td>{{ $item->arrival_date != null ? date('d-m-y H:i:s', strtotime($item->arrival_date)) : '' }}
                    </td>
                    <td>{{ $item->createBy != null ? $item->createBy->username : '' }}</td>
                    <td>{{ $item->editBy != null ? $item->editBy->username : '' }}</td>
                    <td>
                        <a href="#" class="badge badge-primary modalDetail" id="modalDetail"
                            data-id="{{ $item->id }}" data-toggle="modal" data-target="#exampleModal">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="{{ route('pengiriman.ubah', $item->id) }}" class="badge badge-success">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('pengiriman.hapus', $item->id) }}" class="d-inline" method="post">
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
                <td colspan="8" class="text-center"><b>Pengiriman tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- modal for detail --}}
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets//JS/myHelper.js') }}"></script>
