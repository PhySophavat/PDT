@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Page Title -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">គ្រប់គ្រងគ្រូបង្រៀន</h1>
        <a href="{{ route('teacher.create') }}" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition font-semibold">
            + បន្ថែមគ្រូថ្មី
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Teacher Table -->
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b font-medium">ល.រ</th>
                    <th class="px-4 py-2 border-b font-medium">ឈ្មោះ</th>
                    <th class="px-4 py-2 border-b font-medium">អ៊ីម៉ែល</th>
                    <th class="px-4 py-2 border-b font-medium">មុខវិជ្ជា</th>
                    <th class="px-4 py-2 border-b font-medium">កម្រិត</th>
                    <th class="px-4 py-2 border-b font-medium text-center">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $index => $teacher)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2 border-b">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-b">{{ $teacher->user->name }}</td>
                        <td class="px-4 py-2 border-b">{{ $teacher->user->email }}</td>
                        <td class="px-4 py-2 border-b">{{ $teacher->subject }}</td>
                        <td class="px-4 py-2 border-b">{{ $teacher->class_assigned }}</td>
                        <td class="px-4 py-2 border-b text-center">
                            <a href="{{ route('teacher.edit', $teacher) }}" class="text-blue-600 hover:underline mr-2">
                                កែប្រែ
                            </a>

                            <form action="{{ route('teacher.destroy', $teacher) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline mr-2"
                                    onclick="return confirm('តើអ្នកចង់លុបគ្រូនេះមែនទេ?')">
                                    លុប
                                </button>
                            </form>

                            <a href="{{ route('user.permissions.edit', $teacher->user->id) }}" 
                               class="text-blue-600 hover:underline">
                                ផ្តល់សិទ្ធិ
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                            មិនមានគ្រូបង្រៀននៅឡើយទេ។
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
