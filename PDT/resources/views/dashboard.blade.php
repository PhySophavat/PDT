@extends('layouts.app')

@section('title', 'ទំព័រដើម')
@section('page_title', 'ទំព័រដើម')
@section('page_subtitle', 'មើលស្ថានភាពទូទៅនៃអ្នកប្រើប្រាស់ សិស្ស និងគ្រូបង្រៀនក្នុងប្រព័ន្ធ។')
@section('page_actions')
    <a
        href="{{ route('auth.profile') }}"
        class="hero-panel__icon-link"
        title="{{ Auth::user()->name }} | {{ Auth::user()->email }}"
        aria-label="{{ Auth::user()->name }} | {{ Auth::user()->email }}"
    >
        <i class="fa-solid fa-user"></i>
    </a>

    <button
        type="button"
        class="hero-panel__icon-button"
        data-theme-toggle
        aria-label="Switch theme"
        title="Toggle dark mode"
    >
        <span class="theme-toggle__icon">
            <i data-theme-icon class="fa-solid fa-moon"></i>
        </span>
        <span class="sr-only" data-theme-label>Dark mode</span>
    </button>
@endsection

@section('content')
@php
    $currentUser = Auth::user();
    $roleLabels = $currentUser->roles->pluck('display_name')->filter()->implode(', ');
@endphp

<div class="space-y-6">
    <section class="hero-panel">
        <div class="hero-panel__top">
            <div class="hero-panel__intro">
                <p class="app-main__eyebrow">Overview</p>
                <h2 class="hero-panel__title">សួស្តី {{ Auth::user()->name }}</h2>
                <p class="hero-panel__copy">
                    ផ្ទាំងនេះបង្ហាញទិន្នន័យសំខាន់ៗដែលជួយឲ្យអ្នកតាមដានស្ថានភាពប្រព័ន្ធ និងចូលទៅកាន់មុខងារសំខាន់បានលឿន។
                </p>
            </div>
        </div>

        <div class="hero-panel__actions">
            <a href="{{ route('teacher.index') }}" class="app-chip">
                <i class="bi bi-person-workspace"></i>
                <span>គ្រប់គ្រងគ្រូ</span>
            </a>
            <a href="{{ route('students.index') }}" class="app-chip">
                <i class="bi bi-mortarboard"></i>
                <span>គ្រប់គ្រងសិស្ស</span>
            </a>
            <a href="{{ route('class.index') }}" class="app-chip">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>មើលថ្នាក់</span>
            </a>
        </div>
    </section>

    <section class="stat-grid">
        <article class="stat-card" style="--stat-accent: #6366f1;">
            <div class="stat-card__row">
                <div>
                    <p class="stat-card__label">អ្នកប្រើប្រាស់សរុប</p>
                    <p class="stat-card__value">{{ $users }}</p>
                </div>
                <span class="stat-card__icon">
                    <i class="bi bi-people-fill"></i>
                </span>
            </div>
        </article>

        <article class="stat-card" style="--stat-accent: #0ea5e9;">
            <div class="stat-card__row">
                <div>
                    <p class="stat-card__label">សិស្សសរុប</p>
                    <p class="stat-card__value">{{ $students }}</p>
                </div>
                <span class="stat-card__icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
            </div>
        </article>

        <article class="stat-card" style="--stat-accent: #10b981;">
            <div class="stat-card__row">
                <div>
                    <p class="stat-card__label">គ្រូបង្រៀនសរុប</p>
                    <p class="stat-card__value">{{ $teachers }}</p>
                </div>
                <span class="stat-card__icon">
                    <i class="bi bi-person-workspace"></i>
                </span>
            </div>
        </article>
    </section>

    <section class="info-grid">
        <article class="info-panel">
            <h3 class="info-panel__title">ស្ថិតិសិស្ស</h3>
            <div class="data-list">
                <div class="data-row">
                    <span class="data-row__label">សិស្សប្រុស</span>
                    <span class="data-row__value">{{ $maleStudents }}</span>
                </div>
                <div class="data-row">
                    <span class="data-row__label">សិស្សស្រី</span>
                    <span class="data-row__value">{{ $femaleStudents }}</span>
                </div>
                <div class="data-row">
                    <span class="data-row__label">សរុប</span>
                    <span class="data-row__value">{{ $students }}</span>
                </div>
            </div>
        </article>

        <article class="info-panel">
            <h3 class="info-panel__title">ផ្លូវកាត់</h3>
            <div class="data-list">
                <a href="{{ route('result.index') }}" class="data-row">
                    <span class="data-row__label">លទ្ធផល</span>
                    <span class="data-row__value">មើលតាមថ្នាក់</span>
                </a>
                <a href="{{ route('subject.index') }}" class="data-row">
                    <span class="data-row__label">ពិន្ទុ</span>
                    <span class="data-row__value">គ្រប់គ្រងមុខវិជ្ជា</span>
                </a>
                <a href="{{ route('auth.profile') }}" class="data-row">
                    <span class="data-row__label">គណនី</span>
                    <span class="data-row__value">កែតម្រូវព័ត៌មានផ្ទាល់ខ្លួន</span>
                </a>
            </div>
        </article>
    </section>
</div>
@endsection
