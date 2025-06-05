<div class="section mb-4">
    <h5>Kode PO: {{ $po->code }}</h5>
    <div class="row mb-2">
        <div class="col-md-6">
            <p class="mb-1"><strong>Tanggal Aktif:</strong>
                {{ date('d-m-Y', strtotime($po->activation_date)) }}</p>
            <p class="mb-1"><strong>Supplier:</strong>
                {{ $po->supplier != null ? $po->supplier->name : '' }}</p>
        </div>
        <div class="col-md-6">
            <p class="mb-1"><strong>Tipe Harga:</strong> {{ $po->supplier_type }}</p>
            <p class="mb-1"><strong>Keterangan:</strong> {{ $po->supplier_type }}</p>
            {{-- <p class="mb-1"><strong>Periode:</strong> {{ $roadPermit->bulan }}/{{ $roadPermit->tahun }}</p> --}}
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kualitas</th>
                <th>Panjang</th>
                <th>Diameter Awal</th>
                <th>Diameter Akhir</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @if (count($po->details))
                @foreach ($po->details as $detail)
                    <tr>
                        <td class="text-end">{{ $detail->quality }} </td>
                        <td class="text-end">{{ $detail->length }} </td>
                        <td class="text-end">{{ $detail->diameter_start }} </td>
                        <td class="text-end">{{ $detail->diameter_to }} </td>
                        <td>{{ number_format($detail->price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center"><strong>Detail PO Tidak Ditemukan</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
