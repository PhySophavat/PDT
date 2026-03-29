@extends('layouts.app')

@section('content')
@php
    $currentUser = Auth::user();
    $subject = $student->latestSubject;
    $scoreFields = [
        ['field' => 'math', 'label' => 'គណិតវិទ្យា'],
        ['field' => 'khmer', 'label' => 'ភាសាខ្មែរ'],
        ['field' => 'english', 'label' => 'ភាសាអង់គ្លេស'],
        ['field' => 'history', 'label' => 'ប្រវត្តិវិទ្យា'],
        ['field' => 'geography', 'label' => 'ភូមិវិទ្យា'],
        ['field' => 'chemistry', 'label' => 'គីមីវិទ្យា'],
        ['field' => 'physics', 'label' => 'រូបវិទ្យា'],
        ['field' => 'biology', 'label' => 'ជីវវិទ្យា'],
        ['field' => 'ethics', 'label' => 'សីលធម៌'],
        ['field' => 'sports', 'label' => 'កីឡា'],
    ];
    $canEditScores = $currentUser && $currentUser->canEditAnySubjectField() && $currentUser->canAccessClass((string) $student->grade);
    $backUrl = route('class.show', $student->grade);
    $currentCoefficient = old('coefficient', optional($subject)->coefficient ?: 1);
@endphp

<style>
    .excel-score-wrap {
        border: 1px solid #d7e0ea;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .excel-score-toolbar {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #dbe4ee;
        background: linear-gradient(180deg, #f8fbfe 0%, #eff5fa 100%);
    }

    .excel-score-toolbar-main {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .excel-toolbar-copy {
        flex: 1 1 420px;
        min-width: 280px;
    }

    .excel-toolbar-kicker {
        margin: 0;
        color: #94a3b8;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .excel-toolbar-note {
        margin: 0.45rem 0 0;
        color: #526174;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .excel-coefficient-card {
        flex: 0 0 auto;
        min-width: 320px;
        max-width: 460px;
        margin-left: auto;
        padding: 0.9rem 1rem;
        border-radius: 22px;
        background: #ffffff;
        border: 1px solid #d7e0ea;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    }

    .excel-coefficient-head {
        margin-bottom: 0.75rem;
    }

    .excel-coefficient-title {
        margin: 0;
        color: #0f172a;
        font-size: 0.92rem;
        font-weight: 800;
    }

    .excel-coefficient-row {
        display: block;
    }

    .excel-coefficient-input {
        width: 100%;
        height: 48px;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        background: #ffffff;
        padding: 0 0.9rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        text-align: center;
        box-shadow: none;
    }

    .excel-coefficient-input:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18);
    }

    .excel-score-scroll {
        overflow-x: auto;
        overflow-y: hidden;
    }

    .excel-score-scroll::-webkit-scrollbar {
        height: 12px;
    }

    .excel-score-scroll::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #94a3b8;
    }

    .excel-score-table {
        width: 100%;
        min-width: 1180px;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    .excel-score-table thead,
    .excel-score-table thead tr {
        background: #edf4fa !important;
    }

    .excel-score-table th,
    .excel-score-table td {
        border-right: 1px solid #dbe4ee !important;
        border-bottom: 1px solid #dbe4ee !important;
        padding: 0 !important;
        vertical-align: middle !important;
    }

    .excel-score-table th {
        height: 64px;
        color: #415467 !important;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-align: center;
    }

    .excel-score-table th:first-child,
    .excel-score-table td:first-child {
        border-left: 1px solid #dbe4ee !important;
    }

    .excel-score-table tr:first-child th {
        border-top: 1px solid #dbe4ee !important;
    }

    .excel-sticky {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #ffffff !important;
    }

    .excel-score-table th.excel-sticky {
        z-index: 3;
        background: #edf4fa !important;
    }

    .excel-student-cell {
        min-width: 220px;
        padding: 1rem 1.1rem 1rem 1.6rem !important;
        background: #ffffff !important;
    }

    .excel-student-name {
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .excel-input {
        width: 100% !important;
        min-width: 0 !important;
        height: 62px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: #ffffff !important;
        box-shadow: none !important;
        padding: 0 0.85rem !important;
        color: #0f172a !important;
        font-size: 1.05rem !important;
        font-weight: 700 !important;
        text-align: center;
    }

    .excel-input:focus {
        background: #f8fbff !important;
        box-shadow: inset 0 0 0 2px #60a5fa !important;
    }

    .excel-input::placeholder {
        color: #9aa8b8 !important;
    }

    .excel-input[type='number']::-webkit-outer-spin-button,
    .excel-input[type='number']::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .excel-input[type='number'] {
        -moz-appearance: textfield;
    }

    .excel-locked {
        display: grid;
        place-items: center;
        height: 62px;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .excel-score-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.9rem;
    }

    @media (max-width: 768px) {
        .excel-score-toolbar-main {
            align-items: stretch;
            justify-content: flex-start;
        }

        .excel-coefficient-card {
            min-width: 100%;
            margin-left: 0;
        }

        .excel-score-actions {
            flex-direction: column;
        }
    }
</style>

<div class="container app-content-shell--plain mx-auto px-4 py-6 lg:px-8">
    <div class="space-y-6">
        <div class="flex justify-between items-center gap-4 mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    បញ្ចូលពិន្ទុសិស្ស {{ $student->user->name ?? $student->name ?? 'មិនទាន់មានឈ្មោះសិស្ស' }}
                </h1>
                <p class="text-gray-600 mt-2">
                    <i class="bi bi-envelope"></i> {{ $student->user->email ?? 'មិនមានអ៊ីមែល' }}
                </p>
            </div>

            @if($currentUser->hasRole('admin'))
                <a href="{{ route('user.permissions.edit', $student->user->id) }}"
                   class="inline-flex items-center text-white bg-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-700 transition shadow">
                    <i class="bi bi-shield-check mr-2"></i> គ្រប់គ្រងសិទ្ធិ
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg border border-green-300 flex items-center">
                <i class="bi bi-check-circle-fill mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg border border-red-300 flex items-center">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg border border-red-300">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                <strong>មានបញ្ហា</strong>
                <ul class="mt-2 ml-6 list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($canEditScores)
            <form action="{{ route('subject.store', $student->id) }}" method="POST" class="space-y-5" data-score-form>
                @csrf
                <input type="hidden" name="coefficient_changed" value="0" data-coefficient-changed>

                <div class="excel-score-wrap">
                    <div class="excel-score-toolbar">
                        <div class="excel-score-toolbar-main">
                            <div class="excel-toolbar-copy">
                                <p class="excel-toolbar-kicker">របៀបតារាង</p>
                                <p class="excel-toolbar-note">បញ្ចូលពិន្ទុដោយផ្ទាល់ក្នុងក្រឡា ដូចតារាង Excel ហើយប្រើមេគុណដើម្បីគណនាមធ្យមភាគ។</p>
                            </div>

                            <div class="excel-coefficient-card">
                                <div class="excel-coefficient-head">
                                    <p class="excel-coefficient-title">មេគុណ</p>
                                </div>

                                <div class="excel-coefficient-row">
                                    <input
                                        type="number"
                                        name="coefficient"
                                        id="coefficient"
                                        min="0.01"
                                        step="0.01"
                                        inputmode="decimal"
                                        autocomplete="off"
                                        value="{{ $currentCoefficient }}"
                                        class="excel-coefficient-input"
                                        data-coefficient-input
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="excel-score-scroll">
                        <table class="excel-score-table">
                            <thead>
                                <tr>
                                    <th class="excel-sticky" style="width: 220px;">ឈ្មោះសិស្ស</th>
                                    @foreach($scoreFields as $item)
                                        <th style="width: 120px;">{{ $item['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="excel-sticky excel-student-cell">
                                        <p class="excel-student-name">{{ $student->user->name ?? $student->name ?? 'សិស្ស' }}</p>
                                    </td>

                                    @foreach($scoreFields as $item)
                                        @php
                                            $field = $item['field'];
                                            $canEditField = $currentUser->canEditSubjectField($field);
                                            $value = old($field, optional($subject)->{$field});
                                        @endphp

                                        <td>
                                            @if($canEditField)
                                                <input
                                                    type="number"
                                                    name="{{ $field }}"
                                                    value="{{ $value }}"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    inputmode="decimal"
                                                    class="excel-input"
                                                    data-score-input
                                                    placeholder="0"
                                                >
                                            @else
                                                <div
                                                    class="excel-locked"
                                                    data-locked-score="{{ filled($value) ? $value : 0 }}"
                                                    title="គ្មានសិទ្ធិកែ"
                                                >
                                                    {{ filled($value) ? $value : '-' }}
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="excel-score-actions">
                    <button type="submit"
                            class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                        <i class="bi bi-save mr-2"></i> រក្សាទុកពិន្ទុ
                    </button>
                    <a href="{{ $backUrl }}"
                       class="inline-flex items-center bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition shadow-lg">
                        <i class="bi bi-arrow-left mr-2"></i> ត្រឡប់ក្រោយ
                    </a>
                </div>
            </form>
        @else
            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-triangle-fill text-3xl mr-4"></i>
                    <div>
                        <h3 class="font-bold text-lg mb-2">មិនមានសិទ្ធិចូលប្រើប្រាស់</h3>
                        <p>អ្នកមិនមានសិទ្ធិក្នុងការបញ្ចូលពិន្ទុសិស្សទេ។ សូមទាក់ទងអ្នកគ្រប់គ្រងប្រព័ន្ធ។</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-score-form]');

        if (!form) {
            return;
        }

        const coefficientInput = form.querySelector('[data-coefficient-input]');
        const coefficientChangedInput = form.querySelector('[data-coefficient-changed]');

        const markCoefficientChanged = () => {
            if (coefficientChangedInput) {
                coefficientChangedInput.value = '1';
            }
        };

        coefficientInput?.addEventListener('input', markCoefficientChanged);
        coefficientInput?.addEventListener('change', markCoefficientChanged);
        coefficientInput?.addEventListener('wheel', function (event) {
            if (document.activeElement === this) {
                event.preventDefault();
                this.blur();
            }
        }, { passive: false });
        coefficientInput?.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                event.preventDefault();
            }
        });
        coefficientInput?.addEventListener('focus', function () {
            this.select();
        });
    });
</script>
@endsection
