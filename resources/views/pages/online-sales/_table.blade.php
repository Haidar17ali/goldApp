<div class="table-responsive">

    <table class="table mb-0 table-bordered table-hover">

        <thead class="thead-light">

            <tr>
                <th width="40">

                    <input type="checkbox" id="checkAll">

                </th>

                <th width="50">
                    #
                </th>

                <th width="110">
                    Tanggal
                </th>

                <th width="170">
                    Invoice
                </th>

                <th width="180">
                    Order ID
                </th>

                <th width="220">
                    Pembeli
                </th>

                <th>
                    Produk
                </th>

                <th width="140" class="text-right">
                    Total Pembayaran
                </th>

                <th width="140" class="text-right">
                    Diterima
                </th>

                <th width="140" class="text-right">
                    Harga Nota
                </th>
                <th width="140" class="text-right">
                    Kembalian
                </th>

                <th width="140" class="text-right">
                    Admin
                </th>

                <th width="130">
                    Status
                </th>

                <th width="90">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($transactions as $transaction)

                @php

                    $marketplace = $transaction->transactionMarketplace;

                @endphp

                <tr>
                    <td class="text-center">

                        <input type="checkbox" class="transaction-check" value="{{ $transaction->id }}">

                    </td>

                    <td class="text-center">

                        {{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}

                    </td>

                    <td>

                        {{ date('d-m-Y', strtotime($transaction->transaction_date)) }}

                    </td>

                    <td>

                        <strong>

                            {{ $transaction->invoice_number }}

                        </strong>

                    </td>

                    <td>

                        {{ $marketplace?->order_id ?? '-' }}

                    </td>

                    <td>

                        <strong>

                            {{ $transaction->customer?->name ?? '-' }}

                        </strong>

                        <br>

                        <small class="text-muted">

                            {{ $marketplace?->buyer_phone }}

                        </small>

                    </td>

                    <td>

                        @forelse($transaction->details as $detail)
                            <div class="mb-1">

                                <strong>

                                    {{ $detail->productVariant?->product?->name }}

                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ $detail->productVariant?->karat?->name }}

                                    •

                                    {{ $detail->productVariant?->gram }} gr

                                </small>

                            </div>

                        @empty

                            <span class="text-muted">

                                Tidak ada produk

                            </span>
                        @endforelse

                    </td>

                    <td class="text-right">

                        <strong>

                            Rp {{ number_format($marketplace?->marketplace_total ?? 0, 0, ',', '.') }}

                        </strong>

                    </td>

                    <td class="text-right">

                        <strong class="text-success">

                            Rp {{ number_format($marketplace?->received_amount ?? 0, 0, ',', '.') }}

                        </strong>

                    </td>


                    <td class="text-right">

                        <strong>

                            Rp {{ number_format($transaction->total, 0, ',', '.') }}

                        </strong>

                    </td>
                    <td class="text-right">

                        <strong>

                            Rp
                            {{ number_format($marketplace?->received_amount - $transaction->total, 0, ',', '.') }}

                        </strong>

                    </td>
                    <td class="text-right">

                        <strong>

                            Rp
                            {{ number_format($marketplace?->marketplace_total - $marketplace?->received_amount, 0, ',', '.') }}

                        </strong>

                    </td>

                    <td>

                        @php

                            $status = strtoupper($marketplace?->order_status);

                            $badge = 'secondary';

                            if ($status == 'COMPLETED') {
                                $badge = 'success';
                            } elseif ($status == 'PROCESSING') {
                                $badge = 'warning';
                            } elseif ($status == 'SHIPPED') {
                                $badge = 'info';
                            } elseif ($status == 'CANCELLED') {
                                $badge = 'danger';
                            }

                        @endphp

                        <span class="badge badge-{{ $badge }}">

                            {{ $marketplace?->order_status ?? '-' }}

                        </span>

                    </td>

                    <td>

                        <div class="btn-group">

                            <button class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">

                                Aksi

                            </button>

                            <div class="dropdown-menu dropdown-menu-right">

                                <a href="#" class="dropdown-item">

                                    <i class="mr-2 fas fa-eye"></i>

                                    Detail

                                </a>

                                <a href="{{ route('penjualan.online.edit', ['olshopId' => $id, 'id' => $transaction->id]) }}"
                                    class="dropdown-item">

                                    <i class="mr-2 fas fa-edit"></i>

                                    Edit

                                </a>
                                <a href="#" class="dropdown-item text-danger btn-delete"
                                    data-id="{{ $transaction->id }}"
                                    data-url="{{ route('penjualan.online.hapus', ['olshopId' => $id, 'id' => $transaction->id]) }}">

                                    <i class="mr-2 fas fa-trash"></i>

                                    Hapus

                                </a>

                            </div>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="10" class="py-5 text-center">

                        <i class="mb-3 fas fa-shopping-cart fa-3x text-muted"></i>

                        <br>

                        Belum ada transaksi marketplace.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>
