@extends('layouts.app')

@section('title', 'Score Records')

@section('content')
@php
    $currentUser = Auth::user();
    $scoreFields = [
        ['field' => 'math', 'label' => 'Math'],
        ['field' => 'khmer', 'label' => 'Khmer'],
        ['field' => 'english', 'label' => 'English'],
        ['field' => 'history', 'label' => 'History'],
        ['field' => 'geography', 'label' => 'Geography'],
        ['field' => 'chemistry', 'label' => 'Chemistry'],
        ['field' => 'physics', 'label' => 'Physics'],
        ['field' => 'biology', 'label' => 'Biology'],
        ['field' => 'ethics', 'label' => 'Morality'],
        ['field' => 'sports', 'label' => 'Sports'],
    ];
@endphp

<div class="space-y-6">
    <section class="rounded-[32px] border border-white/70 bg-white/85 px-6 py-7 shadow-[0_24px_50px_rgba(15,23,42,0.08)] backdrop-blur-xl lg:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Subject Area</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Score Records</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Review saved subject records and reopen a student score form when updates are needed.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">
                {{ $subjects->count() }} record{{ $subjects->count() === 1 ? '' : 's' }}
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($subjects as $subject)
        @php
            $student = $subject->student;
            $filledScores = collect($scoreFields)->filter(function ($item) use ($subject) {
                return filled($subject->{$item['field']});
            });
            $average = $filledScores->count() > 0
                ? round($filledScores->sum(fn ($item) => (float) $subject->{$item['field']}) / $filledScores->count(), 1)
                : null;
        @endphp

        <article class="rounded-[30px] border border-white/70 bg-white/90 p-5 shadow-[0_20px_45px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Student</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $student->user->name ?? 'Unknown Student' }}</h2>
                    <div class="mt-3 flex flex-wrap gap-3 text-sm text-slate-500">
                        <span class="rounded-full bg-slate-100 px-4 py-2">Grade: {{ $student->grade ?? 'N/A' }}</span>
                        <span class="rounded-full bg-slate-100 px-4 py-2">{{ $student->user->email ?? 'No email' }}</span>
                        <span class="rounded-full bg-slate-100 px-4 py-2">Filled: {{ $filledScores->count() }}/{{ count($scoreFields) }}</span>
                        <span class="rounded-full bg-slate-100 px-4 py-2">Average: {{ $average !== null ? $average : '--' }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @if($currentUser->canManageScores() && $currentUser->canAccessClass((string) $student->grade))
                        <a href="{{ route('subject.create', $student->id) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-blue-700">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Open Score Form</span>
                        </a>
                    @endif

                    @if($currentUser->hasRole('admin'))
                        <form action="{{ route('subject.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Delete this score record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:-translate-y-0.5 hover:bg-rose-100 sm:w-auto">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                @foreach($scoreFields as $item)
                    @php $value = $subject->{$item['field']}; @endphp
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-900">{{ filled($value) ? $value : '--' }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    @empty
        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white/70 px-6 py-10 text-center text-slate-500">
            <i class="fa-solid fa-folder-open text-3xl"></i>
            <p class="mt-4 text-base font-medium">No score records found.</p>
            <p class="mt-2 text-sm">Create scores from the student list or class pages first.</p>
        </div>
    @endforelse
</div>
@endsection
