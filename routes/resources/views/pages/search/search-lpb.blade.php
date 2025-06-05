<div class="d-flex mb-3 justify-content-end">
    <select id="status-action" class="form-control w-auto mr-2">
        <option value="">-- Pilih Status --</option>
        <option value="Terpakai">Terpakai</option>
        <option value="Setujui">Setujui</option>
        <option value="Terbayar">Terbayar</option>
        <option value="Ditolak">Ditolak</option>
    </select>
    <button type="button" class="btn btn-primary" onclick="updateStatus()">Ubah Status</button>
</div>
<div class="mb-2">
    <span><strong>Dipilih:</strong> <span id="selected-count">0</span> LPB</span>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
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
                    <td>
                        <input type="checkbox" class="lpb-checkbox" value="{{ $lpb->id }}">
                    </td>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($lpb->date)) }}</td>
                    <td>{{ $lpb->roadPermit != null ? date('d-m-Y', strtotime($lpb->roadPermit->date)) : date('d-m-Y', strtotime($lpb->arrival_date)) }}
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
                        @if (!$lpb->used)
                            @can(['lpb.pakai'])
                                <a href="{{ route('lpb.pakai', $lpb->id) }}"
                                    class="badge badge-secondary badge-sm btn-modal-detail">Pakai</a>
                            @endcan
                        @endif
                        @if ($lpb->approved_by == null)
                            @can(['utility.approve-lpb'])
                                <a href="{{ route('utility.approve-lpb', ['modelType' => 'LPB', 'id' => $lpb->id, 'status' => 'Menunggu Pembayaran']) }}"
                                    class="badge badge-sm badge-success"><i class="fas fa-check"></i></a>
                                <a href="{{ route('lpb.ubah', $lpb->id) }}" class="badge badge-sm badge-danger"><i
                                        class="fas fa-times"></i></a>
                            @endcan
                            @can(['lpb.ubah'])
                                <a href="{{ route('lpb.ubah', $lpb->id) }}" class="badge badge-success"><i
                                        class="fas fa-pencil-alt"></i></a>
                            @endcan
                            @can(['lpb.hapus'])
                                <form action="{{ route('lpb.hapus', $lpb->id) }}" class="d-inline"
                                    id="delete{{ $lpb->id }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#" data-id="{{ $lpb->id }}"
                                        class="badge badge-pill badge-delete badge-danger d-inline">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </form>
                            @endcan
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

<script>
    // start select checkbox
    STORAGE_KEY = 'selected_lpb_ids';

    function updateSelectedCount() {
        let selected = getSelectedLPBs();
        $('#selected-count').text(selected.length);
    }

    function loadSelectedCheckboxes() {
        highlightRows();
        let saved = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        $('.lpb-checkbox').each(function() {
            if (saved.includes($(this).val())) {
                $(this).prop('checked', true);
            }
        });
    }

    function updateLocalStorageOnCheckChange() {
        let saved = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        updateSelectedCount();
        $('.lpb-checkbox').on('change', function() {
            highlightRows();
            let id = $(this).val();
            if ($(this).is(':checked')) {
                if (!saved.includes(id)) {
                    saved.push(id);
                }
            } else {
                saved = saved.filter(item => item !== id);
            }
            localStorage.setItem(STORAGE_KEY, JSON.stringify(saved));
            updateSelectedCount(); // <-- ini
        });
    }

    function highlightRows() {
        $('.lpb-checkbox').each(function() {
            let row = $(this).closest('tr');
            if ($(this).is(':checked')) {
                row.addClass('table-active');
            } else {
                row.removeClass('table-active');
            }
        });
    }

    $(document).ready(function() {
        loadSelectedCheckboxes();
        updateLocalStorageOnCheckChange();
        updateSelectedCount(); // <-- ini
        highlightRows();

        $('#select-all').on('change', function() {
            let checked = $(this).is(':checked');
            $('.lpb-checkbox').each(function() {
                $(this).prop('checked', checked).trigger('change');
            });
        });
    });

    function getSelectedLPBs() {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    }
    // end select checkbox

    function updateStatus() {
        let selected = getSelectedLPBs();
        let status = $('#status-action').val();

        if (selected.length === 0) {
            alert('Pilih data terlebih dahulu!');
            return;
        }

        if (!status) {
            alert('Pilih status yang ingin diterapkan!');
            return;
        }

        $.post("{{ route('lpb.update-status-masal') }}", {
            _token: "{{ csrf_token() }}",
            selected: selected,
            status: status
        }, function(response) {
            alert(response.message || 'Status berhasil diubah!');
            localStorage.removeItem(STORAGE_KEY);
            location.reload();
        }).fail(function() {
            alert('Terjadi kesalahan saat menyimpan.');
        });

    }
</script>
