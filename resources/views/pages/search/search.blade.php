<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tanggal Nota</th>
            <th scope="col">Supplier</th>
            <th scope="col">Nopol</th>
            <th scope="col">Tipe</th>
            <th scope="col">Nominal Nota</th>
            <th scope="col">PPH</th>
            <th scope="col">Nominal</th>
            <th scope="col">DP</th>
            <th scope="col">Pelunasan</th>
            <th scope="col">Tgl DP & Pelunasan</th>
            {{-- <th scope="col">Status</th> --}}
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->count())
            @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ date('d-m-Y', strtotime($item->nota_date)) }}</td>
                    <td>{{ optional($item->supplier)->name ?? 'Supplier tidak ditemukan' }}</td>
                    <td>
                        {{ count($item->details) > 0 ? $item->details->first()->nopol : '-' }}
                    </td>
                    <td>
                        {{ $item->dp_type }}{{ $item->children->first()?->dp_type ? ' & ' . $item->children->first()->dp_type : '' }}
                    </td>
                    <td>
                        Rp.{{ $item->details ? money_format($item->details->sum('price')) : 0 }}
                    </td>
                    <td>
                        Rp.{{ $item->details ? money_format($item->details->sum('price') * 0.0025) : 0 }}
                    </td>
                    <td>
                        Rp.{{ $item->details ? money_format($item->details->sum('price') - $item->details->sum('price') * 0.0025) : 0 }}
                    </td>
                    <td>Rp.{{ $item->dp_type == 'DP' ? number_format($item->nominal) : 0 }}</td>
                    <td class="{{ $item->dp_type == 'DP' && $item->children->isEmpty() ? 'text-danger' : '' }}">
                        Rp.
                        @if ($item->dp_type === 'DP' && $item->children->first()?->nominal)
                            {{ number_format($item->children->first()->nominal, 0, ',', '.') }}
                        @elseif ($item->details && $item->dp_type === 'DP' && $item->details->sum('price') > 0)
                            {{ number_format(
                                $item->details->sum('price') - $item->details->sum('price') * 0.0025 - $item->nominal,
                                0,
                                ',',
                                '.',
                            ) }}
                        @else
                            {{ money_format($item->nominal) }}
                        @endif
                    </td>


                    <td>
                        <ul>
                            <li>DP: {{ $item->dp_type == 'DP' ? $item->date : '-' }}</li>
                            <li>Pelunasan:
                                {{ $item->children->first()?->date ? $item->children->first()->date : $item->date }}
                            </li>
                        </ul>
                    </td>

                    {{-- <td>
                        <span
                            class="badge badge-{{ $item->status == 'Pending' ? 'warning' : ($item->status == 'Gagal' ? 'danger' : 'success') }}">
                            {{ $item->status }}
                        </span>
                    </td> --}}
                    <td>
                        {{-- @if ($item->status == 'Pending') --}}
                        {{-- <a href="{{ route('utility.activation-dp', [
                                'modelType' => 'Down_payment',
                                'id' => $item->id,
                                'status' => 'Menunggu Pembayaran',
                            ]) }}"
                                class="badge badge-success">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="{{ route('utility.activation-dp', ['modelType' => 'down_payment', 'id' => $item->id, 'status' => 'Gagal']) }}"
                                class="badge badge-danger">
                                <i class="fas fa-times"></i>
                            </a>
                            || --}}
                        <a href="#" class="badge badge-primary"><i class="fas fa-eye"></i></a>
                        @can('down-payment.ubah')
                            <a href="{{ route('down-payment.ubah', ['id' => $item->id, 'type' => $item->type]) }}"
                                class="badge badge-success">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        @endcan
                        @if (count($item->children) > 0)
                            @if (auth()->user()->hasRole('Super Admin'))
                                @can('down-payment.ubah')
                                    <a href="{{ route('down-payment.ubah', ['id' => $item->children->first()?->id ? $item->children->first()->id : $item->id, 'type' => $item->type]) }}"
                                        class="badge badge-success">
                                        <i class="fas fa-pencil-alt"></i> Pelunasan
                                    </a>
                                @endcan
                                <form
                                    action="{{ route('down-payment.hapus', $item->children->first()?->id ? $item->children->first()->id : $item->id) }}"
                                    class="d-inline" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#" data-id="{{ $item->id }}"
                                        class="badge badge-pill badge-delete badge-danger d-inline">
                                        <i class="fas fa-trash"></i> Pelunasan
                                    </a>
                                </form>
                            @endif
                        @endif
                        <form action="{{ route('down-payment.hapus', $item->id) }}" class="d-inline" method="post">
                            @csrf
                            @method('DELETE')
                            <a href="#" data-id="{{ $item->id }}"
                                class="badge badge-pill badge-delete badge-danger d-inline">
                                <i class="fas fa-trash"></i>
                            </a>
                        </form>
                        {{-- @endif --}}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center"><b>Data down-payment tidak ditemukan!</b></td>
            </tr>
        @endif
    </tbody>
</table>
