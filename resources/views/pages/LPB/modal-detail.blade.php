<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail LPB</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>Kitir: <span id="modalKitir"></span></p>
                        <p>Kode LPB: <span id="modalKode"></span></p>
                        <p>Nama Supplier: <span id="modalSupplier"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p>Nopol: <span id="modalNopol"></span></p>
                        <p>Kendaraan: <span id="modalVehicle"></span></p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk Kode</th>
                            <th>Kualitas</th>
                            <th>Panjang</th>
                            <th>Diameter</th>
                            <th>Jumlah</th>
                            <th>Kubikasi</th>
                            <th>Harga/Kubikasi</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody id="modalDetail">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
