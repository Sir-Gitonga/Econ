@props(['icon', 'title', 'description', 'action' => null, 'actionText' => 'Get Started', 'actionOnclick' => null])

<div class="empty-state">
    <div class="empty-state-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    @if ($action || $actionOnclick)
        @if ($actionOnclick)
            <button onclick="{{ $actionOnclick }}" class="btn btn-primary">
                <span>{{ $actionText }}</span>
            </button>
        @else
            <a href="{{ $action }}" class="btn btn-primary">
                <span>{{ $actionText }}</span>
            </a>
        @endif
    @endif
</div>
