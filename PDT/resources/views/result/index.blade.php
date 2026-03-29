@extends('layouts.app')

@section('content')
@include('partials.class-access-tile-styles')
@php
    $currentUser = Auth::user();
@endphp

<div class="container app-content-shell--plain mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold px-4 py-2 border-b font-medium mb-10 text-gray-900">បញ្ជីលទ្ធផល</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @foreach ($grades as $grade => $sections)
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-1 transition-all">
                <h2 class="text-lg font-semibold text-center mb-4 text-blue-600">ថ្នាក់ {{ $grade }}</h2>

                <div class="flex flex-col gap-2">
                    @foreach ($sections as $section)
                        @php
                            $className = $grade . $section;
                            $hasPermission = $currentUser->canAccessResultClass($className);
                        @endphp

                        @if ($hasPermission)
                            <a
                                href="{{ route('result.show', $className) }}"
                                class="class-access-tile class-access-tile--available rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-300"
                            >
                                <span class="class-access-tile__label">{{ $className }}</span>
                            </a>
                        @else
                            <div
                                class="class-access-tile class-access-tile--locked rounded-lg border border-gray-200 bg-gray-50 text-gray-400"
                                title="You do not have permission to access this class result."
                            >
                                <span class="class-access-tile__label">{{ $className }}</span>
                                <span class="class-access-tile__overlay text-amber-600">
                                    <i class="bi bi-lock-fill"></i>
                                    <span>គ្មានសិទ្ធិ</span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
