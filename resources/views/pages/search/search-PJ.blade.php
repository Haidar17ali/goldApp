<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal Jurnal</th>
            <th scope="col">Kode</th>
            <th scope="col">Total Rupiah</th>
            <th scope="col">Total Terbayar</th>
            <th scope="col">Total Gagal</th>
            <th scope="col">Status</th>
            @if (Auth::id() == 1)
                <th scope="col">Pembuat</th>
                <th scope="col">Pengedit</th>
            @endif
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if (count($data))
            @foreach ($data as $purchase_jurnal)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($purchase_jurnal->date)) }}</td>
                    <td>{{ $purchase_jurnal->pj_code }}</td>
                    <td>{{ hitungTotalPembayaran($purchase_jurnal->allLpbs) }}</td>
                    <td>{{ hitungTotalPembayaran($purchase_jurnal->failedLpbs) }}</td>
                    <td>{{ count($purchase_jurnal->failedLpbs) }}</td>
                    <td>
                        <span
                            class="badge {{ $purchase_jurnal->status == 'Selesai' ? 'badge-success' : 'badge-warning' }}">{{ $purchase_jurnal->status }}
                        </span>
                    </td>
                    </td>
                    @if (Auth::id() == 1)
                        <td>{{ $purchase_jurnal->createdBy != null ? $purchase_jurnal->createdBy->username : '' }}
                        </td>
                        <td>{{ $purchase_jurnal->edit_by != null ? $purchase_jurnal->edit_by->username : '' }}
                        </td>
                    @endif
                    <td>
                        @if ($purchase_jurnal->approved_by == null)
                            <a href="{{ route('utility.approve-lpb', ['modelType' => 'LPB', 'id' => $purchase_jurnal->id, 'status' => 'Pending']) }}"
                                class="badge badge-sm badge-success"><i class="fas fa-check"></i></a>
                            <a href="{{ route('lpb.ubah', $purchase_jurnal->id) }}"
                                class="badge badge-sm badge-danger"><i class="fas fa-times"></i></a>
                            <a href="{{ route('purchase-jurnal.ubah', $purchase_jurnal->id) }}"
                                class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('purchase-jurnal.hapus', $purchase_jurnal->id) }}" class="d-inline"
                                id="delete{{ $purchase_jurnal->id }}" method="post">
                                @csrf
                                @method('DELETE')
                                <a href="#" data-id="{{ $purchase_jurnal->id }}"
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
