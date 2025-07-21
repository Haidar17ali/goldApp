<div class="card mt-3">
    <div class="card-body table-responsive p-0">
        <h5>{{ $periode }}</h5>
        <table class="table table-bordered table-hover text-sm">
            <thead class="bg-primary text-white text-center">
                <tr>
                    <th>Tgl Transfer</th>
                    <th>No SAKR</th>
                    <th>SUPLIER</th>
                    <th>NOPOL</th>
                    <th>Tgl Kirim</th>
                    <th>QTY</th>
                    <th>M3</th>
                    <th>NILAI</th>
                    <th>PPH 22</th>
                    <th>TOTAL DITRANSFER</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedLpbs as $tgl => $lpbs)
                    {{-- Group Header --}}
                    <tr class="bg-light font-weight-bold">
                        <td colspan="10">Tanggal: {{ date('d-m-Y', strtotime($tgl)) }}</td>
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
                            <td>{{ date('d-m-Y', strtotime($row['tgl_transfer'])) }}</td>
                            <td>{{ $row['npwp'] }}/{{ date('m-Y', strtotime($row['tgl_kirim'])) }}/{{ $row['kitir'] }}
                            </td>
                            <td>{{ $row['supplier'] }}</td>
                            <td>{{ $row['nopol'] }}</td>
                            <td>{{ date('d-m-Y', strtotime($row['tgl_kirim'])) }}</td>
                            <td class="text-end">{{ $row['qty'] }}</td>
                            <td class="text-end">{{ $row['m3'], 4 }}</td>
                            <td class="text-end">{{ $row['nilai'], 2 }}</td>
                            <td class="text-end">{{ $row['pph'], 2 }}</td>
                            <td class="text-end">{{ $row['transfer'], 2 }}</td>
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
                        <td colspan="5">Total Tanggal {{ date('d-m-Y', strtotime($tgl)) }}</td>
                        <td class="text-end">{{ $groupQty }}</td>
                        <td class="text-end">{{ $groupM3, 4 }}</td>
                        <td class="text-end">{{ $groupNilai, 2 }}</td>
                        <td class="text-end">{{ $groupPph, 2 }}</td>
                        <td class="text-end">{{ $groupTransfer, 2 }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary font-weight-bold">
                    <td colspan="5">Grand Total</td>
                    <td class="text-end">{{ $grandTotal['qty'] }}</td>
                    <td class="text-end">{{ $grandTotal['m3'], 4 }}</td>
                    <td class="text-end">{{ $grandTotal['nilai'], 2 }}</td>
                    <td class="text-end">{{ $grandTotal['pph'], 2 }}</td>
                    <td class="text-end">{{ $grandTotal['transfer'], 2 }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
