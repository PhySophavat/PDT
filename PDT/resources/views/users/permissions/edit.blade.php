@extends('layouts.app')

@section('title', 'កែសម្រួលសិទ្ធិអ្នកប្រើប្រាស់')
@section('page_title', 'កែសម្រួលសិទ្ធិអ្នកប្រើប្រាស់')
@section('page_subtitle', 'ជ្រើសសិទ្ធិបន្ថែមសម្រាប់គណនីនេះដោយចុចលើកាតនីមួយៗ។')

@php
    $backUrl = $user->teacher
        ? route('teacher.index')
        : ($user->student ? route('students.index') : route('dashboard'));

    $roleLabels = $user->roles->pluck('display_name')->filter()->values();

    $groupMeta = [
        'general' => [
            'label' => 'ទូទៅ',
            'description' => 'សិទ្ធិគ្រប់គ្រងទិន្នន័យ និងមុខងារសំខាន់ៗក្នុងប្រព័ន្ធ',
        ],
        'subjects' => [
            'label' => 'មុខវិជ្ជា',
            'description' => 'សិទ្ធិពាក់ព័ន្ធនឹងមុខវិជ្ជា និងការគ្រប់គ្រងពិន្ទុ',
        ],
        'classes' => [
            'label' => 'ថ្នាក់',
            'description' => 'សិទ្ធិចូលមើលទិន្នន័យរបស់ថ្នាក់នីមួយៗ ចាប់ពីថ្នាក់ទី 7 ឡើងទៅ',
        ],
    ];

    $resolvePermissionGroup = function ($permission): string {
        if (str_starts_with($permission->name, 'class-')) {
            return 'classes';
        }

        if (str_starts_with($permission->name, 'subject-')) {
            return 'subjects';
        }

        return 'general';
    };

    $resolvePermissionSortKey = function ($permission): string {
        if (str_starts_with($permission->name, 'class-')) {
            $classCode = strtoupper(substr($permission->name, 6));
            preg_match('/^(\d+)([A-Z]+)$/', $classCode, $matches);

            $grade = isset($matches[1]) ? (int) $matches[1] : 99;
            $section = $matches[2] ?? 'ZZ';

            return sprintf('%02d-%s', $grade, $section);
        }

        return strtolower((string) ($permission->name ?? ''));
    };

    $permissionGroups = collect(array_keys($groupMeta))->mapWithKeys(function ($groupKey) use ($permissions, $resolvePermissionGroup, $resolvePermissionSortKey) {
        $groupPermissions = $permissions
            ->filter(fn ($permission) => $resolvePermissionGroup($permission) === $groupKey)
            ->sortBy($resolvePermissionSortKey)
            ->values();

        return $groupPermissions->isNotEmpty() ? [$groupKey => $groupPermissions] : [];
    });
@endphp

@section('page_actions')
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        <span>ត្រឡប់ក្រោយ</span>
    </a>
@endsection

@section('content')
<style>
    .permission-page {
        display: grid;
        gap: 1.5rem;
    }

    .permission-summary {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        border-radius: 28px;
        padding: 1.35rem;
        border: 1px solid var(--border);
        background: linear-gradient(
            145deg,
            color-mix(in srgb, var(--surface) 82%, var(--surface-contrast) 18%),
            color-mix(in srgb, var(--surface-strong) 92%, transparent)
        );
        box-shadow: var(--shadow-md);
    }

    .permission-summary__panel {
        border-radius: 22px;
        padding: 1rem 1.1rem;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
    }

    .permission-summary__eyebrow {
        margin: 0 0 0.45rem;
        font-size: 0.76rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .permission-summary__value {
        margin: 0;
        font-family: 'Manrope', 'Kantumruy Pro', sans-serif;
        font-size: 1.35rem;
        line-height: 1.15;
        color: var(--text);
    }

    .permission-summary__meta {
        margin: 0.45rem 0 0;
        color: var(--muted);
        word-break: break-word;
    }

    .permission-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        margin-top: 0.8rem;
    }

    .permission-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.42rem 0.75rem;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--accent) 12%, var(--surface-muted));
        color: var(--text);
        font-size: 0.88rem;
        font-weight: 700;
    }

    .permission-form {
        display: grid;
        gap: 1.25rem;
    }

    .permission-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-radius: 24px;
        padding: 1rem 1.1rem;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface) 94%, transparent);
        box-shadow: var(--shadow-md);
    }

    .permission-toolbar__copy {
        margin: 0;
        color: var(--muted);
    }

    .permission-toolbar__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .permission-toolbar__count {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.55rem 0.85rem;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
        font-weight: 700;
        color: var(--text);
    }

    .permission-group {
        display: grid;
        gap: 1rem;
        border-radius: 28px;
        padding: 1.2rem;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface) 94%, transparent);
        box-shadow: var(--shadow-md);
    }

    .permission-group__header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .permission-group__title {
        margin: 0;
        font-family: 'Manrope', 'Kantumruy Pro', sans-serif;
        font-size: 1.1rem;
        color: var(--text);
    }

    .permission-group__subtitle {
        margin: 0.35rem 0 0;
        color: var(--muted);
    }

    .permission-group__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.2rem;
        height: 2.2rem;
        padding: 0 0.75rem;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--accent) 12%, var(--surface-muted));
        color: var(--text);
        font-weight: 800;
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .permission-tile {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
        min-height: 118px;
        padding: 1rem 1.05rem;
        border-radius: 22px;
        border: 1px solid var(--border);
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
        cursor: pointer;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.14);
    }

    .permission-tile:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: color-mix(in srgb, var(--accent) 28%, var(--border));
    }

    .permission-tile__input {
        position: absolute;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
        opacity: 0 !important;
    }

    .permission-tile__check {
        width: 1.55rem;
        height: 1.55rem;
        flex-shrink: 0;
        display: inline-grid;
        place-items: center;
        border-radius: 0.55rem;
        border: 1px solid color-mix(in srgb, var(--border) 82%, var(--accent) 18%);
        background: color-mix(in srgb, var(--surface) 95%, transparent);
        color: transparent;
        margin-top: 0.12rem;
    }

    .permission-tile__input:checked + .permission-tile__check {
        border-color: transparent;
        background: linear-gradient(135deg, var(--accent), var(--accent-strong));
        color: #ffffff;
        box-shadow: 0 16px 26px color-mix(in srgb, var(--accent) 30%, transparent);
    }

    .permission-tile__body {
        display: grid;
        gap: 0.35rem;
        min-width: 0;
    }

    .permission-tile__name {
        font-weight: 800;
        color: var(--text);
        line-height: 1.35;
    }

    .permission-tile__meta {
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        word-break: break-word;
    }

    .permission-tile__description {
        color: var(--muted);
        font-size: 0.92rem;
        line-height: 1.45;
    }

    .permission-tile__input:checked ~ .permission-tile__body .permission-tile__name {
        color: color-mix(in srgb, var(--accent-strong) 88%, var(--text));
    }

    .permission-tile:has(.permission-tile__input:checked) {
        border-color: color-mix(in srgb, var(--accent) 34%, var(--border));
        background: linear-gradient(
            145deg,
            color-mix(in srgb, var(--accent) 10%, var(--surface-muted)),
            color-mix(in srgb, var(--surface) 96%, transparent)
        );
        box-shadow: 0 18px 32px color-mix(in srgb, var(--accent) 12%, transparent);
    }

    .permission-empty {
        border: 1px dashed var(--border);
        border-radius: 24px;
        padding: 1.4rem;
        background: color-mix(in srgb, var(--surface-muted) 92%, transparent);
        color: var(--muted);
        text-align: center;
    }

    .permission-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-end;
        padding-top: 0.25rem;
    }

    @media (max-width: 640px) {
        .permission-toolbar {
            align-items: stretch;
        }

        .permission-toolbar__actions,
        .permission-actions {
            width: 100%;
        }

        .permission-toolbar__actions .btn,
        .permission-actions .btn {
            flex: 1 1 0;
        }
    }
</style>

<div class="permission-page">
    <section class="permission-summary">
        <article class="permission-summary__panel">
            <p class="permission-summary__eyebrow">គណនី</p>
            <h2 class="permission-summary__value">{{ $user->name }}</h2>
            <p class="permission-summary__meta">{{ $user->email }}</p>

            @if ($roleLabels->isNotEmpty())
                <div class="permission-badges">
                    @foreach ($roleLabels as $roleLabel)
                        <span class="permission-badge">
                            <i class="bi bi-person-badge"></i>
                            <span>{{ $roleLabel }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="permission-summary__panel">
            <p class="permission-summary__eyebrow">សិទ្ធិបច្ចុប្បន្ន</p>
            <h2 class="permission-summary__value">
                <span data-selected-count>{{ count($userPermissions ?? []) }}</span> / {{ $permissions->count() }}
            </h2>
            <p class="permission-summary__meta">ចំនួនសិទ្ធិផ្ទាល់ដែលបានផ្ដល់ឱ្យគណនីនេះ</p>
        </article>

        <article class="permission-summary__panel">
            <p class="permission-summary__eyebrow">ការណែនាំ</p>
            <h2 class="permission-summary__value">ជ្រើសតាមកាត</h2>
            <p class="permission-summary__meta">ចុចលើកាតដើម្បីបើក ឬដកសិទ្ធិ ហើយបន្ទាប់មកចុចប៊ូតុងរក្សាទុក</p>
        </article>
    </section>

    @if ($errors->any())
        <div class="guest-alert guest-alert--error">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('user.permissions.update', $user->id) }}" class="permission-form">
        @csrf
        @method('PUT')

        <section class="permission-toolbar">
            <p class="permission-toolbar__copy">ជ្រើសសិទ្ធិដែលត្រូវផ្ដល់បន្ថែមទៅឱ្យអ្នកប្រើប្រាស់នេះ។</p>

            <div class="permission-toolbar__actions">
                <span class="permission-toolbar__count">
                    <i class="bi bi-check2-square"></i>
                    <span><span data-selected-count>{{ count($userPermissions ?? []) }}</span> បានជ្រើស</span>
                </span>
                <button type="button" class="btn btn-outline-secondary" data-select-all>
                    <i class="bi bi-check-all"></i>
                    <span>ជ្រើសទាំងអស់</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" data-clear-all>
                    <i class="bi bi-x-lg"></i>
                    <span>ដកទាំងអស់</span>
                </button>
            </div>
        </section>

        @if ($permissions->isEmpty())
            <div class="permission-empty">
                មិនទាន់មានសិទ្ធិនៅក្នុងប្រព័ន្ធទេ។
            </div>
        @else
            @foreach ($permissionGroups as $groupKey => $groupPermissions)
                <section class="permission-group">
                    <header class="permission-group__header">
                        <div>
                            <h3 class="permission-group__title">{{ $groupMeta[$groupKey]['label'] }}</h3>
                            <p class="permission-group__subtitle">{{ $groupMeta[$groupKey]['description'] }}</p>
                        </div>

                        <span class="permission-group__count">{{ $groupPermissions->count() }}</span>
                    </header>

                    <div class="permission-grid">
                        @foreach ($groupPermissions as $permission)
                            <label class="permission-tile" for="perm_{{ $permission->id }}">
                                <input
                                    class="permission-tile__input"
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    id="perm_{{ $permission->id }}"
                                    {{ in_array($permission->id, $userPermissions ?? []) ? 'checked' : '' }}
                                >
                                <span class="permission-tile__check">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                                <span class="permission-tile__body">
                                    <span class="permission-tile__name">{{ $permission->display_name }}</span>
                                    <span class="permission-tile__meta">{{ $permission->name }}</span>

                                    @if ($permission->description)
                                        <span class="permission-tile__description">{{ $permission->description }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif

        <div class="permission-actions">
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                បោះបង់
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg"></i>
                <span>រក្សាទុកការផ្លាស់ប្តូរ</span>
            </button>
        </div>
    </form>
</div>

<script>
    (() => {
        const scope = document.currentScript.closest('.app-content');

        if (!scope) {
            return;
        }

        const checkboxes = Array.from(scope.querySelectorAll('.permission-tile__input'));
        const countTargets = scope.querySelectorAll('[data-selected-count]');
        const selectAllButton = scope.querySelector('[data-select-all]');
        const clearAllButton = scope.querySelector('[data-clear-all]');

        const updateCount = () => {
            const selectedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

            countTargets.forEach((target) => {
                target.textContent = selectedCount;
            });
        };

        selectAllButton?.addEventListener('click', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });

            updateCount();
        });

        clearAllButton?.addEventListener('click', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });

            updateCount();
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateCount);
        });

        updateCount();
    })();
</script>
@endsection
