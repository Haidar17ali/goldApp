@php
    $unreadNotifications = Auth::user()->notifications()->where('is_read', false)->latest()->take(5)->get();
    $unreadCount = $unreadNotifications->count();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        @if ($unreadCount > 0)
            <span class="badge badge-danger navbar-badge">{{ $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header">{{ $unreadCount }} Notifikasi Baru</span>
        <div class="dropdown-divider"></div>
        @foreach ($unreadNotifications as $notif)
            <a href="{{ $notif->link }}" class="dropdown-item">
                <div class="d-flex align-items-start">
                    @switch($notif->type)
                        @case('po_created')
                            <i class="fas fa-file-alt text-primary mr-3 mt-1"></i>
                        @break

                        @default
                            <i class="fas fa-bell text-secondary mr-3 mt-1"></i>
                    @endswitch
                    <div style="min-width: 0; max-width: 220px;">
                        <div class="font-weight-bold text-truncate" style="max-width: 100%;">
                            {{ $notif->title }}
                        </div>
                        <div class="text-muted small text-truncate" style="max-width: 100%;">
                            {{ $notif->message ?? '-' }}
                        </div>
                        <div class="text-muted text-sm">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </a>
            <div class="dropdown-divider"></div>
        @endforeach
        <a href="#" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
    </div>
</li>
