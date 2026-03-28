@extends('layouts.app')

@section('title', 'Profile')
@section('page_title', 'ប្រវត្តិរូប')
@section('page_subtitle', 'ពិនិត្យព័ត៌មានគណនី និងព័ត៌មានសិស្សដែលភ្ជាប់ជាមួយអ្នកប្រើប្រាស់នេះ។')

@section('content')
<div class="space-y-6">
    <div class="info-grid">
        <section class="info-panel">
            <h2 class="info-panel__title">ព័ត៌មានអ្នកប្រើប្រាស់</h2>
            <div class="data-list">
                <div class="data-row">
                    <span class="data-row__label">ឈ្មោះ</span>
                    <span class="data-row__value">{{ Auth::user()->name }}</span>
                </div>
                <div class="data-row">
                    <span class="data-row__label">អ៊ីមែល</span>
                    <span class="data-row__value">{{ Auth::user()->email }}</span>
                </div>
            </div>
        </section>

        @if (Auth::user()->student)
            <section class="info-panel">
                <h2 class="info-panel__title">ព័ត៌មានសិស្ស</h2>
                <div class="data-list">
                    <div class="data-row">
                        <span class="data-row__label">ថ្នាក់</span>
                        <span class="data-row__value">{{ Auth::user()->student->grade }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ថ្ងៃខែឆ្នាំកំណើត</span>
                        <span class="data-row__value">{{ Auth::user()->student->date_of_birth ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ភេទ</span>
                        <span class="data-row__value">{{ Auth::user()->student->gender ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ទីកន្លែងស្នាក់នៅ</span>
                        <span class="data-row__value">
                            {{ implode(', ', array_filter([
                                Auth::user()->student->village,
                                Auth::user()->student->commune,
                                Auth::user()->student->district,
                                Auth::user()->student->province,
                            ])) ?: '-' }}
                        </span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ទូរស័ព្ទ</span>
                        <span class="data-row__value">{{ Auth::user()->student->phone ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">វិទ្យាល័យ</span>
                        <span class="data-row__value">{{ Auth::user()->student->high_school ?? '-' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ម្តាយ</span>
                        <span class="data-row__value">
                            {{ Auth::user()->student->mother_name ?? '-' }}
                            @if (Auth::user()->student->mother_phone)
                                <br>{{ Auth::user()->student->mother_phone }}
                            @endif
                        </span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">ឪពុក</span>
                        <span class="data-row__value">
                            {{ Auth::user()->student->father_name ?? '-' }}
                            @if (Auth::user()->student->father_phone)
                                <br>{{ Auth::user()->student->father_phone }}
                            @endif
                        </span>
                    </div>
                </div>
            </section>
        @endif
    </div>

    @if (!Auth::user()->student)
        <div class="app-empty">
            មិនមានព័ត៌មានសិស្សដែលភ្ជាប់ជាមួយគណនីនេះទេ។
        </div>
    @endif
</div>
@endsection
