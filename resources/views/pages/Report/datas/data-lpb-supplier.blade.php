<div class="card mt-3">
    <div class="card-body table-responsive p-0">
        <h5>{{ $periode }}</h5>
        <table class="table table-bordered table-hover text-sm">
            <thead class="bg-primary text-white text-center">
                <tr>
                    <th>No SAKR</th>
                    <th>SUPLIER</th>
                    <th>NOPOL</th>
                    <th>{{ $headerDate }}</th>
                    <th>QTY</th>
                    <th>M3</th>
                    <th>NILAI</th>
                    <th>PPH 22</th>
                    <th>TOTAL DITRANSFER</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedLpbs as $supplier => $lpbs)
                    {{-- Group Header --}}
                    <tr class="bg-light font-weight-bold">
                        <td colspan="8">{{ $supplier }}</td>
                    </tr>

                    @php
                        $groupQty = 0;
                        $groupM3 = 0;
                        $groupNilai = 0;
                        $groupPph = 0;
                        $groupTransfer = 0;
                    @endphp

                    @foreach ($lpbs as $key => $row)
                        <tr>
                            <td>{{ $row['npwp'] }}/
                                {{ date('m-Y', strtotime($row['tgl_kirim'])) }}/{{ $row['kitir'] }}</td>
                            <td>{{ $row['supplier'] }}</td>
                            <td>{{ $row['nopol'] }}</td>
                            <td>{{ date('d-m-Y', strtotime($row['tgl_kirim'])) }}</td>
                            <td class="text-end">{{ number_format($row['qty']) }}</td>
                            <td class="text-end">{{ number_format($row['m3'], 4) }}</td>
                            <td class="text-end">{{ number_format($row['nilai'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['pph'], 2) }}</td>
                            <td class="text-end">{{ number_format($row['transfer'], 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('laporan.lpb-supplier-export-excel', [
                                    'nopol' => $row['nopol'],
                                    'supplier' => $row['supplierId'],
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'date_by' => $dateBy,
                                    'type' => 'BKL',
                                ]) }}"
                                    id="printLpbExcel" class="badge badge-success ">
                                    <i class="fas fa-file-excel"></i> BKL
                                </a>
                                <a href="{{ route('laporan.lpb-supplier-export-excel', [
                                    'nopol' => $row['nopol'],
                                    'supplier' => $row['supplierId'],
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'date_by' => $dateBy,
                                ]) }}"
                                    id="printLpbExcel" class="badge badge-success ">
                                    <i class="fas fa-file-excel"></i>
                                </a>
                                <a href="{{ route('laporan.lpb-supplier-export-pdf', [
                                    'nopol' => $row['nopol'],
                                    'supplier' => $row['supplierId'],
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'date_by' => $dateBy,
                                ]) }}"
                                    id="printLpb" class="badge badge-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a class="badge badge-sm badge-info" data-toggle="modal" data-target="#modalDetail"
                                    onclick="#"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>

                        @php
                            $groupQty += $row['qty'];
                            $groupM3 += $row['m3'];
                            $groupNilai += $row['nilai'];
                            $groupPph += $row['pph'];
                            $groupTransfer += $row['transfer'];
                        @endphp
                    @endforeach

                    {{-- Group Total --}}
                    <tr class="table-success font-weight-bold">
                        <td colspan="4">{{ $supplier }} Total</td>
                        <td class="text-end">{{ number_format($groupQty) }}</td>
                        <td class="text-end">{{ number_format($groupM3, 4) }}</td>
                        <td class="text-end">{{ number_format($groupNilai, 2) }}</td>
                        <td class="text-end">{{ number_format($groupPph, 2) }}</td>
                        <td class="text-end">{{ number_format($groupTransfer, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary font-weight-bold">
                    <td colspan="4">Grand Total</td>
                    <td class="text-end">{{ number_format($grandTotal['qty']) }}</td>
                    <td class="text-end">{{ number_format($grandTotal['m3'], 4) }}</td>
                    <td class="text-end">{{ number_format($grandTotal['nilai'], 2) }}</td>
                    <td class="text-end">{{ number_format($grandTotal['pph'], 2) }}</td>
                    <td class="text-end">{{ number_format($grandTotal['transfer'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
