@props(['value', 'label', 'subtitle' => null, 'gradient' => true])

<div class="dashboard-card {{ $attributes->get('class') }}">
    <div class="dashboard-card-content">
        <div class="dashboard-card-label">{{ $label }}</div>
        <div class="dashboard-card-value">{{ $value }}</div>
        @if ($subtitle)
            <div class="dashboard-card-subtitle">{{ $subtitle }}</div>
        @endif
    </div>
</div>
