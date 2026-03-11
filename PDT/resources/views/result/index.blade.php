@extends('layouts.app')
@section('content')

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold px-4 py-2 border-b font-medium mb-10 text-gray-900">បញ្ជីលទ្ធផល</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @foreach ($grades as $grade => $sections)
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-1 transition-all">
                <h2 class="text-lg font-semibold text-center mb-4 text-blue-600">ថ្នាក់ {{ $grade }}</h2>
                
                <div class="flex flex-col gap-2">
                    @foreach ($sections as $section)
                        @php
                            $className = $grade . $section;
                            $permissionName = 'class-' . $className;

                            $isAssignedTeacher = false;
                            if (Auth::user()->teacher && Auth::user()->teacher->class_assigned) {
                                $assignedClasses = array_map('trim', explode(',', Auth::user()->teacher->class_assigned));
                                $isAssignedTeacher = in_array($className, $assignedClasses);
                            }

                            $hasPermission = Auth::user()->hasPermission($permissionName) 
                                          || Auth::user()->hasPermission('view-results')
                                          || Auth::user()->hasPermission('view-scores')
                                          || Auth::user()->hasRole('admin')
                                          || $isAssignedTeacher;
                        @endphp

                        @if($hasPermission)
                            <a href="{{ route('result.show', $className) }}" 
                               class="block rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-300 text-center font-medium py-2 transition-all">
                                {{ $className }}
                            </a>
                        @else
                            <div class="block rounded-lg border border-gray-200 bg-gray-50 text-gray-400 text-center font-medium py-2">
                                {{ $className }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
