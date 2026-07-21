<div class="mt-3 shadow-sm card">

    <div class="card-header bg-light">
        <strong>Pembayaran Marketplace</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <label>Harga Nota</label>

                <input type="text" id="invoiceTotal" class="form-control form-control-lg" readonly>

            </div>

            <div class="col-md-4">

                <label>Uang Dibayar Pembeli</label>

                <input type="number" class="form-control form-control-lg" id="buyerPaid" name="marketplace_total"
                    min="0" step="0.01"
                    value="{{ old('marketplace_total', $transaction->transactionMarketplace->marketplace_total ?? 0) }}">

            </div>

            <div class="col-md-4">

                <label>Uang yang Kita Terima</label>

                <input type="number" class="form-control form-control-lg" id="receivedAmount" name="received_amount"
                    min="0" step="0.01"
                    value="{{ old('received_amount', $transaction->transactionMarketplace->received_amount ?? 0) }}">

            </div>

        </div>

        <hr>

        <div class="text-center row">

            <div class="col-md-6">

                <div class="p-3 border rounded bg-light">

                    <small class="text-muted">

                        Biaya Marketplace

                    </small>

                    <h3 class="mt-2 text-danger">

                        <span id="marketplaceFee">

                            Rp 0

                        </span>

                    </h3>

                </div>

            </div>

            <div class="col-md-6">

                <div class="p-3 border rounded bg-success">

                    <small class="text-white">

                        Kembalian Pembeli

                    </small>

                    <h3 class="mt-2 text-white">

                        <span id="changeAmount">

                            Rp 0

                        </span>

                    </h3>

                </div>

            </div>

        </div>

    </div>

</div>
