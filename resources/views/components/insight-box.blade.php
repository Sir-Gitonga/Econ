@props(['message', 'type' => 'info', 'icon' => null, 'action' => null, 'actionText' => null])

@php
    $bgColors = [
        'info' => 'background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%); color: #0c4a6e;',
        'success' => 'background: linear-gradient(135deg, #d1fae5 0%, #dcfce7 100%); color: #047857;',
        'warning' => 'background: linear-gradient(135deg, #fef3c7 0%, #fef08a 100%); color: #b45309;',
    ];

    $iconDefault = [
        'info' => 'fas fa-lightbulb',
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-circle',
    ];

    $style = $bgColors[$type] ?? $bgColors['info'];
    $iconClass = $icon ?? $iconDefault[$type] ?? 'fas fa-info-circle';
@endphp

<div style="display: flex; align-items: center; gap: 1rem; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; {{ $style }}">
    <div style="font-size: 1.5rem; min-width: 24px;">
        <i class="{{ $iconClass }}"></i>
    </div>
    <div style="flex: 1;">
        <p style="margin: 0; font-weight: 500;">{{ $message }}</p>
    </div>
    @if ($action && $actionText)
        <a href="{{ $action }}" style="white-space: nowrap; text-decoration: none; font-weight: 600; cursor: pointer;">
            {{ $actionText }} →
        </a>
    @endif
</div>
