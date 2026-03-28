@php($toggleClass = $toggleClass ?? '')

<button type="button" class="theme-toggle {{ $toggleClass }}" data-theme-toggle aria-label="Switch theme">
    <span class="theme-toggle__icon">
        <i data-theme-icon class="fa-solid fa-moon"></i>
    </span>
    <span data-theme-label>Dark mode</span>
</button>
