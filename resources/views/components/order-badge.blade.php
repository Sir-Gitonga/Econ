@props(['status'])

@php
    $badgeClass = match($status) {
        'delivered' => 'badge-success',
        'ordered', 'pending' => 'badge-ordered',
        'canceled', 'cancelled' => 'badge-danger',
        'processing' => 'badge-warning',
        default => 'badge-ordered',
    };
    
    $badgeText = match($status) {
        'delivered' => 'Delivered',
        'ordered' => 'Ordered',
        'pending' => 'Pending',
        'canceled' => 'Canceled',
        'cancelled' => 'Canceled',
        'processing' => 'Processing',
        default => ucfirst($status),
    };
    
    $badgeIcon = match($status) {
        'delivered' => 'fas fa-check-circle',
        'ordered' => 'fas fa-clock',
        'pending' => 'fas fa-hourglass-half',
        'canceled' => 'fas fa-times-circle',
        'cancelled' => 'fas fa-times-circle',
        'processing' => 'fas fa-spinner',
        default => 'fas fa-info-circle',
    };
@endphp

<span class="badge {{ $badgeClass }}">
    <i class="{{ $badgeIcon }}"></i>
    <span>{{ $badgeText }}</span>
</span>
