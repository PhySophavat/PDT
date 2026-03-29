@php
    $toggleClass = $toggleClass ?? '';
    $iconOnly = $iconOnly ?? false;
@endphp

<button type="button" class="theme-toggle {{ $iconOnly ? 'theme-toggle--icon-only' : '' }} {{ $toggleClass }}" data-theme-toggle aria-label="Switch theme">
    <span class="theme-toggle__icon">
        <i data-theme-icon class="fa-solid fa-moon"></i>
    </span>
    @if($iconOnly)
        <span class="sr-only" data-theme-label>Dark mode</span>
    @else
        <span data-theme-label>Dark mode</span>
    @endif
</button>
