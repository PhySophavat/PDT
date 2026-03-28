<script>
    (() => {
        const storageKey = 'pdt-theme';
        const root = document.documentElement;

        const getInitialTheme = () => {
            const savedTheme = window.localStorage.getItem(storageKey);

            if (savedTheme === 'light' || savedTheme === 'dark') {
                return savedTheme;
            }

            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const applyTheme = (theme) => {
            root.dataset.theme = theme;
            root.style.colorScheme = theme;

            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                const icon = button.querySelector('[data-theme-icon]');
                const label = button.querySelector('[data-theme-label]');

                if (icon) {
                    icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                }

                if (label) {
                    label.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
                }

                button.setAttribute(
                    'aria-label',
                    theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
                );
            });
        };

        root.dataset.theme = getInitialTheme();
        root.style.colorScheme = root.dataset.theme;

        document.addEventListener('DOMContentLoaded', () => {
            applyTheme(root.dataset.theme);

            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';

                    window.localStorage.setItem(storageKey, nextTheme);
                    applyTheme(nextTheme);
                });
            });
        });
    })();
</script>

<style>
    :root {
        color-scheme: light;
        --bg: #edf4fb;
        --bg-gradient-a: rgba(14, 165, 233, 0.2);
        --bg-gradient-b: rgba(13, 148, 136, 0.16);
        --surface: rgba(255, 255, 255, 0.88);
        --surface-strong: #ffffff;
        --surface-muted: #f5f9fc;
        --surface-contrast: #e0f2fe;
        --text: #0f172a;
        --muted: #5b6b81;
        --border: rgba(148, 163, 184, 0.28);
        --accent: #0891b2;
        --accent-strong: #0f766e;
        --success: #059669;
        --danger: #e11d48;
        --warning: #d97706;
        --shadow-lg: 0 24px 60px rgba(15, 23, 42, 0.12);
        --shadow-md: 0 16px 35px rgba(15, 23, 42, 0.08);
    }

    html[data-theme='dark'] {
        color-scheme: dark;
        --bg: #08111f;
        --bg-gradient-a: rgba(34, 211, 238, 0.16);
        --bg-gradient-b: rgba(20, 184, 166, 0.14);
        --surface: rgba(9, 16, 31, 0.84);
        --surface-strong: #0f172a;
        --surface-muted: #111c31;
        --surface-contrast: #0b1c2f;
        --text: #e2e8f0;
        --muted: #94a3b8;
        --border: rgba(148, 163, 184, 0.2);
        --accent: #22d3ee;
        --accent-strong: #14b8a6;
        --success: #34d399;
        --danger: #fb7185;
        --warning: #fbbf24;
        --shadow-lg: 0 24px 60px rgba(2, 6, 23, 0.46);
        --shadow-md: 0 16px 35px rgba(2, 6, 23, 0.32);
    }

    * {
        box-sizing: border-box;
    }

    html {
        background: var(--bg);
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: 'Kantumruy Pro', 'Manrope', sans-serif;
        background:
            radial-gradient(circle at top left, var(--bg-gradient-a), transparent 32%),
            radial-gradient(circle at bottom right, var(--bg-gradient-b), transparent 28%),
            linear-gradient(180deg, var(--bg), color-mix(in srgb, var(--bg) 88%, #ffffff 12%));
        color: var(--text);
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    html[data-theme='dark'] body {
        background:
            radial-gradient(circle at top left, var(--bg-gradient-a), transparent 32%),
            radial-gradient(circle at bottom right, var(--bg-gradient-b), transparent 28%),
            linear-gradient(180deg, #08111f, #040b16 65%, #08111f);
    }

    a,
    button,
    input,
    textarea,
    select {
        transition:
            background-color 0.2s ease,
            color 0.2s ease,
            border-color 0.2s ease,
            box-shadow 0.2s ease,
            transform 0.2s ease;
    }

    a {
        color: inherit;
        text-decoration: none;
    }

    button {
        cursor: pointer;
    }

    button:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    ::selection {
        background: color-mix(in srgb, var(--accent) 25%, transparent);
    }

    :focus-visible {
        outline: 3px solid color-mix(in srgb, var(--accent) 45%, transparent);
        outline-offset: 2px;
    }

    .sidebar::-webkit-scrollbar {
        width: 7px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: color-mix(in srgb, var(--accent) 28%, var(--border));
    }

    .app-shell {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
        gap: 1.25rem;
        padding: 1rem;
    }

    @media (min-width: 1024px) {
        .app-shell {
            flex-direction: row;
            align-items: stretch;
            padding: 1.5rem;
        }
    }

    .app-sidebar {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 28px;
        background: color-mix(in srgb, var(--surface-strong) 84%, transparent);
        box-shadow: var(--shadow-lg);
        backdrop-filter: blur(18px);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (min-width: 1024px) {
        .app-sidebar {
            position: sticky;
            top: 1.5rem;
            width: 19rem;
            min-height: calc(100vh - 3rem);
            max-height: calc(100vh - 3rem);
        }
    }

    .app-brand,
    .guest-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.9rem;
        font-weight: 700;
        color: var(--text);
    }

    .app-brand__mark,
    .guest-brand__mark {
        width: 2.9rem;
        height: 2.9rem;
        display: inline-grid;
        place-items: center;
        border-radius: 1rem;
        background: linear-gradient(135deg, var(--accent), var(--accent-strong));
        color: #ffffff;
        font-size: 0.95rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        box-shadow: 0 18px 30px color-mix(in srgb, var(--accent) 35%, transparent);
    }

    .app-brand__text {
        display: flex;
        flex-direction: column;
        gap: 0.12rem;
    }

    .app-brand__title {
        font-size: 1rem;
        font-weight: 700;
    }

    .app-brand__subtitle {
        font-size: 0.75rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .sidebar-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-radius: 24px;
        padding: 1rem;
        border: 1px solid color-mix(in srgb, var(--border) 80%, var(--accent) 20%);
        background: linear-gradient(
            145deg,
            color-mix(in srgb, var(--surface-contrast) 55%, var(--surface-strong) 45%),
            color-mix(in srgb, var(--surface) 88%, transparent)
        );
    }

    .sidebar-user__avatar {
        width: 3.8rem;
        height: 3.8rem;
        flex-shrink: 0;
        display: inline-grid;
        place-items: center;
        border-radius: 1.25rem;
        background: linear-gradient(
            135deg,
            color-mix(in srgb, var(--accent) 18%, transparent),
            color-mix(in srgb, var(--accent-strong) 18%, transparent)
        );
        color: var(--accent-strong);
        font-size: 1.4rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
    }

    .sidebar-user__meta {
        min-width: 0;
    }

    .sidebar-user__label {
        margin: 0 0 0.2rem;
        font-size: 0.78rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .sidebar-user__name {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar-user__link {
        font-size: 0.9rem;
        color: var(--accent-strong);
    }

    .workspace-dropdown {
        border: 1px solid color-mix(in srgb, var(--border) 80%, var(--accent) 20%);
        border-radius: 24px;
        background: linear-gradient(
            145deg,
            color-mix(in srgb, var(--surface-contrast) 55%, var(--surface-strong) 45%),
            color-mix(in srgb, var(--surface) 88%, transparent)
        );
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .workspace-dropdown__summary {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
    }

    .workspace-dropdown__head {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .workspace-dropdown__body {
        min-width: 0;
    }

    .workspace-dropdown__label {
        margin: 0 0 0.2rem;
        font-size: 0.78rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .workspace-dropdown__title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .workspace-dropdown__meta {
        margin: 0.2rem 0 0;
        font-size: 0.9rem;
        color: var(--muted);
    }

    .workspace-dropdown__menu {
        display: grid;
        gap: 0.75rem;
        padding: 0 1rem 1rem;
    }

    .workspace-dropdown__account {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 0.9rem 1rem;
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
    }

    .workspace-dropdown__account-meta {
        min-width: 0;
    }

    .workspace-dropdown__account-name {
        margin: 0;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .workspace-dropdown__account-email {
        margin: 0.15rem 0 0;
        font-size: 0.88rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .workspace-dropdown__link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 0.85rem 1rem;
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
        color: var(--text);
        font-weight: 600;
    }

    .workspace-dropdown__link:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .workspace-dropdown .theme-toggle {
        width: 100%;
        justify-content: space-between;
        box-shadow: none;
    }

    .sidebar-label {
        margin: 0.3rem 0 0.4rem;
        padding: 0 0.4rem;
        font-size: 0.78rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .menu-link {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        border-radius: 18px;
        padding: 0.9rem 1rem;
        border: 1px solid transparent;
        color: var(--muted);
        font-weight: 600;
    }

    .menu-link i {
        width: 1.2rem;
        text-align: center;
    }

    .menu-link:hover,
    .menu-link.is-active {
        color: var(--text);
        background: color-mix(in srgb, var(--surface-muted) 82%, var(--accent) 18%);
        border-color: color-mix(in srgb, var(--border) 80%, var(--accent) 20%);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .menu-link.is-active {
        box-shadow: 0 18px 32px color-mix(in srgb, var(--accent) 14%, transparent);
    }

    .logout-button {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        border: 1px solid color-mix(in srgb, var(--danger) 28%, var(--border));
        border-radius: 18px;
        padding: 0.9rem 1rem;
        background: color-mix(in srgb, var(--danger) 12%, var(--surface));
        color: color-mix(in srgb, var(--danger) 80%, var(--text));
        font-weight: 700;
    }

    .logout-button:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .theme-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 0.72rem 1rem;
        background: color-mix(in srgb, var(--surface) 92%, transparent);
        color: var(--text);
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
    }

    .theme-toggle:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .theme-toggle__icon {
        width: 1.9rem;
        height: 1.9rem;
        display: inline-grid;
        place-items: center;
        border-radius: 999px;
        background: color-mix(in srgb, var(--accent) 14%, var(--surface-muted));
        color: var(--accent-strong);
    }

    .app-main {
        min-width: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .app-main__header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 1rem 1.25rem;
        background: color-mix(in srgb, var(--surface-strong) 84%, transparent);
        box-shadow: var(--shadow-md);
        backdrop-filter: blur(18px);
    }

    .app-main__actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .app-main__eyebrow,
    .guest-kicker {
        margin: 0 0 0.35rem;
        font-size: 0.78rem;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .app-main__title,
    .guest-title {
        margin: 0;
        font-family: 'Manrope', 'Kantumruy Pro', sans-serif;
        font-size: clamp(1.7rem, 2.4vw, 2.5rem);
        line-height: 1.1;
        color: var(--text);
    }

    .app-main__subtitle,
    .guest-subtitle {
        margin: 0.55rem 0 0;
        max-width: 48rem;
        color: var(--muted);
        font-size: 1rem;
    }

    .app-content {
        flex: 1;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 1.2rem;
        background: color-mix(in srgb, var(--surface-strong) 70%, transparent);
        box-shadow: var(--shadow-lg);
        backdrop-filter: blur(18px);
        overflow: hidden;
    }

    @media (min-width: 1024px) {
        .app-content {
            padding: 1.5rem;
        }
    }

    .app-content .container,
    .app-content .bg-white,
    .app-content .bg-white\/90,
    .app-content .bg-white\/85,
    .app-content .bg-white\/70,
    .app-content .bg-gray-50,
    .app-content .bg-gray-100,
    .app-content .card,
    .app-card,
    .stat-card,
    .info-panel {
        border: 1px solid var(--border) !important;
        background: color-mix(in srgb, var(--surface) 95%, transparent) !important;
        box-shadow: var(--shadow-md) !important;
    }

    .app-content .text-gray-900,
    .app-content .text-gray-800,
    .app-content .text-gray-700,
    .app-content .text-slate-900 {
        color: var(--text) !important;
    }

    .app-content .text-gray-600,
    .app-content .text-gray-500,
    .app-content .text-slate-500,
    .app-content .text-slate-400 {
        color: var(--muted) !important;
    }

    .app-content .border,
    .app-content .border-t,
    .app-content .border-b,
    .app-content .border-l,
    .app-content .border-r,
    .app-content .border-gray-100,
    .app-content .border-gray-200,
    .app-content .border-gray-300,
    .app-content .border-slate-300,
    .app-content .border-green-200,
    .app-content .border-green-300,
    .app-content .border-blue-200,
    .app-content .border-blue-300,
    .app-content .border-white\/70,
    .app-content .border-dashed {
        border-color: var(--border) !important;
    }

    .app-content .card-header,
    .app-content thead,
    .app-content thead tr,
    .app-content .bg-gray-200 {
        background: color-mix(in srgb, var(--surface-muted) 82%, var(--accent) 18%) !important;
    }

    .app-content table {
        width: 100%;
        border-collapse: collapse;
        border-color: var(--border) !important;
    }

    .app-content th,
    .app-content td {
        border-color: var(--border) !important;
        vertical-align: middle;
    }

    .app-content tbody tr {
        transition: background-color 0.2s ease;
    }

    .app-content tbody tr:hover,
    .app-content .hover\:bg-gray-50:hover {
        background: color-mix(in srgb, var(--surface-muted) 82%, var(--accent) 18%) !important;
    }

    .app-content input,
    .app-content select,
    .app-content textarea,
    .app-content .form-control,
    .app-content .form-select,
    .guest-card input,
    .guest-card select,
    .guest-card textarea {
        width: 100%;
        border: 1px solid var(--border) !important;
        border-radius: 16px !important;
        background: color-mix(in srgb, var(--surface-muted) 94%, transparent) !important;
        color: var(--text) !important;
        box-shadow: none !important;
    }

    .app-content input::placeholder,
    .app-content textarea::placeholder,
    .guest-card input::placeholder,
    .guest-card textarea::placeholder {
        color: color-mix(in srgb, var(--muted) 78%, transparent);
    }

    .app-content label,
    .guest-card label,
    .app-content .form-label {
        color: var(--muted);
        font-weight: 600;
    }

    .app-content .btn,
    .app-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border: 1px solid transparent;
        border-radius: 16px;
        padding: 0.85rem 1.25rem;
        font-weight: 700;
    }

    .app-content .btn-primary,
    .app-content .btn-success,
    .app-button--primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-strong)) !important;
        color: #ffffff !important;
        box-shadow: 0 18px 28px color-mix(in srgb, var(--accent) 28%, transparent);
    }

    .app-content .btn-outline-secondary,
    .app-button--secondary {
        border-color: var(--border) !important;
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent) !important;
        color: var(--text) !important;
    }

    .app-content .btn:hover,
    .app-button:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .app-content .badge {
        border-radius: 999px;
        padding: 0.5rem 0.75rem;
    }

    .app-content .bg-success,
    .app-content .badge.bg-success {
        background: color-mix(in srgb, var(--success) 72%, #000000 0%) !important;
        color: #ffffff !important;
    }

    .app-content .bg-light,
    .app-content .badge.bg-light {
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent) !important;
        color: var(--text) !important;
    }

    .app-content .text-blue-600,
    .app-content .text-blue-700,
    .app-content .text-sky-600 {
        color: color-mix(in srgb, var(--accent) 65%, var(--text)) !important;
    }

    .app-content .text-green-600,
    .app-content .text-green-700 {
        color: color-mix(in srgb, var(--success) 75%, var(--text)) !important;
    }

    .app-content .text-red-600,
    .app-content .text-red-700 {
        color: color-mix(in srgb, var(--danger) 80%, var(--text)) !important;
    }

    .app-content .bg-green-100,
    .guest-alert--success {
        border: 1px solid color-mix(in srgb, var(--success) 35%, var(--border)) !important;
        background: color-mix(in srgb, var(--success) 14%, var(--surface)) !important;
        color: color-mix(in srgb, var(--success) 82%, var(--text)) !important;
    }

    .app-content .bg-red-100,
    .guest-alert--error {
        border: 1px solid color-mix(in srgb, var(--danger) 34%, var(--border)) !important;
        background: color-mix(in srgb, var(--danger) 12%, var(--surface)) !important;
        color: color-mix(in srgb, var(--danger) 84%, var(--text)) !important;
    }

    .stat-grid,
    .info-grid {
        display: grid;
        gap: 1rem;
    }

    .stat-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .info-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .hero-panel,
    .stat-card,
    .info-panel {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 1.25rem;
    }

    .hero-panel::after,
    .stat-card::after {
        content: '';
        position: absolute;
        inset: auto -10% -40% auto;
        width: 9rem;
        height: 9rem;
        border-radius: 999px;
        background: radial-gradient(circle, color-mix(in srgb, var(--accent) 12%, transparent), transparent 70%);
        pointer-events: none;
    }

    .hero-panel {
        background: linear-gradient(
            145deg,
            color-mix(in srgb, var(--surface) 76%, var(--surface-contrast) 24%),
            color-mix(in srgb, var(--surface-strong) 92%, transparent)
        );
    }

    .hero-panel__title,
    .info-panel__title {
        margin: 0;
        font-family: 'Manrope', 'Kantumruy Pro', sans-serif;
        font-size: 1.2rem;
        color: var(--text);
    }

    .hero-panel__copy {
        margin: 0.75rem 0 0;
        max-width: 42rem;
        color: var(--muted);
    }

    .hero-panel__top {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.25rem;
    }

    .hero-panel__intro {
        flex: 1 1 28rem;
        min-width: 0;
    }

    .hero-panel__icon-link,
    .hero-panel__icon-button {
        width: 3.2rem;
        height: 3.2rem;
        display: inline-grid;
        place-items: center;
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: color-mix(in srgb, var(--surface-muted) 90%, transparent);
        color: var(--text);
        box-shadow: var(--shadow-md);
    }

    .hero-panel__icon-link:hover,
    .hero-panel__icon-button:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }

    .hero-panel__icon-link i,
    .hero-panel__icon-button i {
        font-size: 1.05rem;
    }

    .hero-panel__icon-button {
        padding: 0;
    }

    .hero-panel__icon-button .theme-toggle__icon {
        width: 100%;
        height: 100%;
        background: transparent;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .hero-panel__account {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 1rem;
        background: color-mix(in srgb, var(--surface-muted) 90%, transparent);
        box-shadow: var(--shadow-md);
    }

    .hero-panel__account-meta {
        min-width: 0;
    }

    .hero-panel__account-label {
        margin: 0 0 0.15rem;
        font-size: 0.76rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .hero-panel__account-name {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hero-panel__account-email {
        margin: 0.2rem 0 0;
        font-size: 0.9rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hero-panel__theme {
        width: 100%;
        justify-content: space-between;
        background: color-mix(in srgb, var(--surface-muted) 90%, transparent);
        box-shadow: var(--shadow-md);
    }

    .hero-panel__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .app-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.7rem 0.95rem;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
        color: var(--text);
        font-weight: 600;
    }

    .stat-card {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        inset: auto 0 0 0;
        height: 4px;
        background: var(--stat-accent, var(--accent));
    }

    .stat-card__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .stat-card__label {
        margin: 0;
        font-size: 0.8rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .stat-card__value {
        margin: 0;
        font-family: 'Manrope', 'Kantumruy Pro', sans-serif;
        font-size: clamp(2rem, 3vw, 2.7rem);
        line-height: 1;
        color: var(--text);
    }

    .stat-card__icon {
        width: 3.3rem;
        height: 3.3rem;
        display: inline-grid;
        place-items: center;
        border-radius: 1.1rem;
        font-size: 1.25rem;
        color: var(--stat-accent, var(--accent));
        background: color-mix(in srgb, var(--stat-accent, var(--accent)) 12%, var(--surface-muted));
    }

    .data-list {
        display: grid;
        gap: 0.8rem;
        margin-top: 1rem;
    }

    .data-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 16px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
    }

    .data-row__label {
        color: var(--muted);
        font-weight: 600;
    }

    .data-row__value {
        color: var(--text);
        text-align: right;
        word-break: break-word;
    }

    @media (max-width: 640px) {
        .data-row {
            flex-direction: column;
        }

        .data-row__value {
            text-align: left;
        }
    }

    .app-empty {
        border: 1px dashed var(--border);
        border-radius: 24px;
        padding: 1.25rem;
        background: color-mix(in srgb, var(--surface-muted) 90%, transparent);
        color: var(--muted);
        text-align: center;
    }

    .guest-shell {
        min-height: 100vh;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center;
        justify-content: center;
    }

    .guest-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        width: min(100%, 42rem);
        margin: 0 auto;
    }

    .guest-card {
        width: min(100%, 42rem);
        margin: 0 auto;
        border-radius: 32px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-strong) 86%, transparent);
        box-shadow: var(--shadow-lg);
        backdrop-filter: blur(18px);
        padding: 1.5rem;
    }

    @media (min-width: 640px) {
        .guest-card {
            padding: 2rem;
        }
    }

    .guest-form {
        display: grid;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .guest-field {
        display: grid;
        gap: 0.55rem;
    }

    .guest-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        border-radius: 18px;
        padding: 0.9rem 1rem;
        margin-top: 1rem;
    }

    .guest-footer {
        margin-top: 1.25rem;
        color: var(--muted);
        text-align: center;
    }

    .guest-footer a {
        color: var(--accent-strong);
        font-weight: 700;
    }

    .w-full {
        width: 100%;
    }
</style>
