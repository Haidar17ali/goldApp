<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal PO</th>
            <th scope="col">Kode PO</th>
            <th scope="col">Supplier</th>
            @if ($type == 'Sengon')
                <th scope="col">Jenis Supplier</th>
            @endif
            <th scope="col">Status</th>
            @if ($type != 'Sengon')
                <th scope="col">PPN</th>
                <th scope="col">DP</th>
                <th scope="col">Pemesan</th>
            @endif
            <th scope="col">Pembuat</th>
            <th scope="col">Pengedit</th>
            <th scope="col">Penyetuju</th>
            <th scope="col">Disetujui Tgl</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if (count($data))
            @foreach ($data as $po)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($po->date)) }}</td>
                    <td>{{ $po->po_code }}</td>
                    <td>{{ $po->supplier != null ? $po->supplier->name : '' }}</td>
                    @if ($type == 'Sengon')
                        <td>{{ $po->supplier_type }}</td>
                    @endif
                    <td>
                        <span
                            class="badge {{ $po->status == 'Aktif' ? 'badge-success' : ($po->status == 'Pending' ? 'badge-warning' : 'badge-danger') }}">{{ $po->status }}</span>
                    </td>
                    @if ($type != 'Sengon')
                        <td>{{ $po->ppn }}</td>
                        <td>{{ $po->dp }}</td>
                        <td>{{ $po->order_by != null ? $po->order_by->fullname : '' }}</td>
                    @endif
                    <td>{{ $po->createdBy != null ? $po->createdBy->username : '' }}</td>
                    <td>{{ $po->edit_by != null ? $po->edit_by->username : '' }}</td>
                    <td>{{ $po->approvedBy != null ? $po->approvedBy->username : 'PO Belum Disetujui!' }}
                    <td>{{ $po->approved_at != null ? date('d-m-Y', strtotime($po->approved_at)) : '' }}
                    </td>
                    <td>

                        @if (
                            $po->approved_by != null &&
                                $po->status != 'Tidak Disetujui' &&
                                $po->status != 'Aktif' &&
                                $po->status != 'Gagal' &&
                                $po->status != 'Non-Aktif')
                            @if (date('d-m-Y', strtotime($po->activation_date)) <= date('d-m-Y', strtotime(now()->toDateString())))
                                <a href="{{ route('utility.activation-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Aktif']) }}"
                                    class="badge badge-success">Aktifkan</a>
                                <a href="{{ route('utility.activation-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Gagal']) }}"
                                    class="badge badge-danger">Gagal</a>
                            @else
                                <span class="text-muted">Belum bisa diaktifkan</span>
                            @endif
                        @endif
                        @if ($po->approved_by != null && $po->status != 'Tidak Disetujui' && $po->status != 'Gagal' && $po->status == 'Aktif')
                            <a href="{{ route('utility.activation-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Non-Aktif']) }}"
                                class="badge badge-danger">Non-Aktifkan</a>
                        @endif
                        @if ($po->approved_by == null)
                            <a href="{{ route('utility.approve-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Aktif']) }}"
                                class="badge badge-success"><i class="fas fa-check"></i></a>
                            <a href="{{ route('utility.approve-po', ['modelType' => 'PO', 'id' => $po->id, 'status' => 'Tidak Disetujui']) }}"
                                class="badge badge-danger"><i class="fas fa-times"></i></a>
                            ||
                            <a href="{{ route('purchase-order.ubah', ['type' => $type, 'id' => $po->id]) }}"
                                class="badge badge-success"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('purchase-order.hapus', $po->id) }}" class="d-inline"
                                id="delete{{ $po->id }}" method="post">
                                @csrf
                                @method('DELETE')
                                <a href="#" data-id="{{ $po->id }}"
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
