<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal LPB</th>
            <th scope="col">Tanggal Kedatangan</th>
            <th scope="col">No Kitir</th>
            <th scope="col">Nopol</th>
            <th scope="col">Supplier</th>
            <th scope="col">NPWP</th>
            <th scope="col">Grader & Tally</th>
            <th scope="col">Status</th>
            <th scope="col">Penyetuju</th>
            @if (Auth::id() == 1)
                <th scope="col">Pembuat</th>
                <th scope="col">Pengedit</th>
            @endif
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if (count($data))
            @foreach ($data as $lpb)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($lpb->date)) }}</td>
                    <td>{{ $lpb->roadPermit != null ? date('d-m-Y', strtotime($lpb->roadPermit->date)) : '' }}
                    </td>
                    <td>{{ $lpb->no_kitir }}</td>
                    <td>{{ $lpb->nopol }}</td>
                    <td>{{ $lpb->supplier != null ? $lpb->supplier->name : '' }}</td>
                    <td>{{ $lpb->npwp != null ? $lpb->npwp->name : '' }}</td>
                    <td>{{ $lpb->grader != null ? $lpb->grader->fullname : '' }} &
                        {{ $lpb->tally != null ? $lpb->tally->fullname : '' }}
                    </td>
                    <td>
                        <span
                            class="badge {{ $lpb->status == 'Terbayar' ? 'badge-success' : ($lpb->status == 'Pending' ? 'badge-warning' : 'badge-danger') }}
                        ">{{ $lpb->status }}
                        </span>
                    </td>
                    <td>{{ $lpb->approvalBy != null ? $lpb->approvalBy->username : 'Menunggu Persetujuan' }}
                    </td>
                    @if (Auth::id() == 1)
                        <td>{{ $lpb->createdBy != null ? $lpb->createdBy->username : '' }}</td>
                        <td>{{ $lpb->edit_by != null ? $lpb->edit_by->username : '' }}</td>
                    @endif
                    <td>
                        <a href="#" class="badge badge-info badge-sm btn-modal-detail" data-toggle="modal"
                            data-id="{{ $lpb->id }}" data-target="#detailModal"><i class="fas fa-eye"></i></a>
                        @if ($lpb->used == false)
                            <a href="{{ route('lpb.pakai', $lpb->id) }}"
                                class="badge badge-secondary badge-sm btn-modal-detail">Pakai</a>
                        @endif
                        @if ($lpb->approved_by == null)
                            <a href="{{ route('utility.approve-lpb', ['modelType' => 'LPB', 'id' => $lpb->id, 'status' => 'Pending']) }}"
                                class="badge badge-sm badge-success"><i class="fas fa-check"></i></a>
                            <a href="{{ route('lpb.ubah', $lpb->id) }}" class="badge badge-sm badge-danger"><i
                                    class="fas fa-times"></i></a>
                            <a href="{{ route('lpb.ubah', $lpb->id) }}" class="badge badge-success"><i
                                    class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('lpb.hapus', $lpb->id) }}" class="d-inline"
                                id="delete{{ $lpb->id }}" method="post">
                                @csrf
                                @method('DELETE')
                                <a href="#" data-id="{{ $lpb->id }}"
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
                <td colspan="14" class="text-center"><b>Data tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
