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
                        <a href="#" class="badge badge-primary modalDetail" id="modalDetail"
                            data-id="{{ $item->id }}" data-toggle="modal" data-target="#exampleModal">
                            <i class="fas fa-eye"></i> Detail
                        </a>
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
<script>
    $(document).ready(function() {

        function loadData(datas) {
            $("#modalTitle").text("Kode Potongan: " + datas.code);
            // Isi body modal
            let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> ${datas.date}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Nama Penjahit:</strong> ${datas.tailor_name}</p>    
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Ukuran</th>
                                <th>Warna</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

            datas.details.forEach(detail => {
                html += `
                    <tr style="text-transform:uppercase;">
                        <td>${detail.product.name}</td>
                        <td>${detail.size.code}</td>
                        <td>${detail.color.name}</td>
                        <td>${detail.qty}</td>
                        <td>${detail.status}</td>
                        <td><a href="#" class="btn btn-success actionButton" data-id="${detail.id}"><i class="fas fa-check"></i> Selesai</a></td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            $("#modalBody").html(html);
        }

        $(".modalDetail").on("click", function(e) {
            e.preventDefault();
            let id = $(this).data("id");
            console.log(id);

            let url = "{{ route('utility.getById') }}";
            let model = "Cutting";
            let relation = ["details.product", "details.size", "details.color"]

            let data = {
                id: id,
                model: model,
                relation: relation,
            }

            let datas = loadWithData(url, data);

            loadData(datas);

        });

        $(document).on("click", ".actionButton", function(e) {
            e.preventDefault();
            let button = $(this);
            let id = button.data("id");

            $.ajax({
                url: "{{ route('cutting.updateDetail') }}",
                method: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.success) {
                        // update kolom status langsung di tabel
                        let row = button.closest("tr");
                        row.find("td:nth-child(5)").html(
                            `<span class="badge bg-success">${res.status}</span>`);

                        // disable tombol supaya tidak bisa diklik lagi
                        button.replaceWith(
                            `<span class="text-muted">Selesai (${res.finish_at})</span>`
                            );
                    }
                }
            });
        });

    })
</script>
