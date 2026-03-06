@foreach ($accounts as $account)
    <tr data-id="{{ $account->id }}" data-parent="{{ $parent }}" class="coa-row level-{{ $level }}">

        <td>

            @if ($account->childrenRecursive->count())
                <button class="btn btn-xs btn-primary toggle">
                    +
                </button>
            @endif

        </td>

        <td>{{ $account->code }}</td>

        <td style="padding-left: {{ $level * 25 }}px">
            {{ ucwords($account->name) }}
        </td>

        <td>{{ ucwords($account->category) }}</td>

        <td>

            <a href="{{ route('coa.ubah', $account->id) }}" class="badge badge-primary">
                <i class="fas fa-edit"></i>
            </a>

            <form action="{{ route('coa.hapus', $account->id) }}" method="POST" style="display:inline-block"
                class="form-delete" data-has-child="{{ $account->childrenRecursive->count() ? 1 : 0 }}">

                @csrf
                @method('DELETE')

                <button type="submit" class="border-0 badge badge-danger">
                    <i class="fas fa-trash"></i>
                </button>

            </form>

        </td>

    </tr>


    @if ($account->childrenRecursive->count())
        @include('pages.coa.row', [
            'accounts' => $account->childrenRecursive,
            'level' => $level + 1,
            'parent' => $account->id,
        ])
    @endif
@endforeach
