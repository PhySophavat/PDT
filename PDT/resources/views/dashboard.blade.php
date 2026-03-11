@extends('layouts.app')

@section('content')
<div class="p-10 min-h-screen bg-gray-50">

    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="bi bi-speedometer2 text-blue-600"></i>
            ផ្ទាំងគ្រប់គ្រងប្រព័ន្ធ
        </h1>
        <p class="text-gray-600 mt-2 text-lg">
            សួស្តី <span class="font-semibold text-blue-700">{{ Auth::user()->name }}</span> 👋 សូមស្វាគមន៍មកកាន់ផ្ទាំងគ្រប់គ្រង។
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Users -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 text-sm font-semibold uppercase">អ្នកប្រើប្រាស់សរុប</h2>
                    <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $users }}</h1>
                </div>
                <i class="bi bi-people-fill text-indigo-500 text-4xl"></i>
            </div>
        </div>

        <!-- Students -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 text-sm font-semibold uppercase">សិស្សសរុប</h2>
                    <h1 class="text-3xl font-bold text-blue-600 mt-2">{{ $students }}</h1>
                </div>
                <i class="bi bi-mortarboard-fill text-blue-500 text-4xl"></i>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-gray-500 text-sm font-semibold uppercase">គ្រូបង្រៀនសរុប</h2>
                    <h1 class="text-3xl font-bold text-green-600 mt-2">{{ $teachers }}</h1>
                </div>
                <i class="bi bi-person-workspace text-green-500 text-4xl"></i>
            </div>
        </div>
    </div>

    {{-- <!-- Gender Chart Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-pie-chart text-pink-500"></i>
            បម្រែបម្រួលភេទសិស្ស
        </h2>

        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
            <canvas id="genderChart" class="w-64 h-64"></canvas>

            <div class="space-y-2 text-gray-700 text-lg">
                <p>👦 សិស្សប្រុស៖ <span class="font-semibold text-blue-600">{{ $maleStudents }}</span></p>
                <p>👧 សិស្សស្រី៖ <span class="font-semibold text-pink-600">{{ $femaleStudents }}</span></p>
                <p>សរុប៖ <span class="font-semibold text-gray-800">{{ $students }}</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('genderChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['សិស្សប្រុស', 'សិស្សស្រី'],
            datasets: [{
                data: [{{ $maleStudents }}, {{ $femaleStudents }}],
                backgroundColor: ['#3b82f6', '#ec4899'],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 14 },
                        color: '#374151'
                    }
                }
            }
        }
    });
</script> --}}
@endsection
