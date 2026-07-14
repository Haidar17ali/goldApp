<div class="card card-outline card-secondary">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-2">

                    <label>Dari Tanggal</label>

                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">

                </div>

                <div class="col-md-2">

                    <label>Sampai Tanggal</label>

                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">

                </div>

                <div class="col-md-2">

                    <label>Status Order</label>

                    <select name="order_status" class="form-control">

                        <option value="">Semua</option>

                        <option value="UNPAID" @selected(request('order_status') == 'UNPAID')>
                            Belum Dibayar
                        </option>

                        <option value="PROCESSING" @selected(request('order_status') == 'PROCESSING')>
                            Diproses
                        </option>

                        <option value="SHIPPED" @selected(request('order_status') == 'SHIPPED')>
                            Dikirim
                        </option>

                        <option value="DELIVERED" @selected(request('order_status') == 'DELIVERED')>
                            Diterima
                        </option>

                        <option value="COMPLETED" @selected(request('order_status') == 'COMPLETED')>
                            Selesai
                        </option>

                        <option value="CANCELLED" @selected(request('order_status') == 'CANCELLED')>
                            Dibatalkan
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <label>Status Pembayaran</label>

                    <select name="payment_status" class="form-control">

                        <option value="">Semua</option>

                        <option value="UNPAID" @selected(request('payment_status') == 'UNPAID')>
                            Belum Dibayar
                        </option>

                        <option value="PAID" @selected(request('payment_status') == 'PAID')>
                            Sudah Dibayar
                        </option>

                        <option value="REFUND" @selected(request('payment_status') == 'REFUND')>
                            Refund
                        </option>

                    </select>

                </div>

                <div class="col-md-4">

                    <label>Pencarian</label>

                    <div class="input-group">

                        <input type="text" name="search" class="form-control"
                            placeholder="Invoice / Order ID / Pembeli..." value="{{ request('search') }}">

                        <div class="input-group-append">

                            <button class="btn btn-primary">

                                <i class="fas fa-search"></i>

                            </button>

                            @if (request()->hasAny(['search', 'date_from', 'date_to', 'order_status', 'payment_status']))
                                <a href="{{ route('penjualan.online.index', $marketplace->code) }}"
                                    class="btn btn-secondary">

                                    <i class="fas fa-sync"></i>

                                </a>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>
