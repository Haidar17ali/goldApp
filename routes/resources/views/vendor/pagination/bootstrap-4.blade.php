@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        aria-label="@lang('pagination.previous')">&laquo;</a>
                </li>
            @endif

            {{-- Halaman Awal (Selalu Tampilkan) --}}
            @if ($paginator->currentPage() > 3)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($paginator->currentPage() > 4)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @elseif ($paginator->currentPage() > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
            @elseif ($paginator->currentPage() == 1)
                <li class="page-item active" aria-current="page">
                    <span class="page-link">1</span>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @php
                $start = max(1, $paginator->currentPage() - 2);
                $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
            @endphp

            @for ($i = $start; $i <= $end; $i++)
                @if ($i > 1 && $i < $paginator->lastPage())
                    <li class="page-item {{ $i == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Halaman Akhir (Selalu Tampilkan) --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @elseif ($paginator->currentPage() < $paginator->lastPage())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @elseif ($paginator->currentPage() == $paginator->lastPage())
                <li class="page-item active" aria-current="page">
                    <span class="page-link">{{ $paginator->lastPage() }}</span>
                </li>
            @endif

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="@lang('pagination.next')">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
