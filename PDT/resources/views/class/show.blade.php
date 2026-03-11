@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <div class="bg-white ">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-people-fill text-blue-600"></i>
                បញ្ជីសិស្សថ្នាក់ {{ $className }}
            </h2>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('students.create') }}" 
               class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition font-semibold">
                <i class="bi bi-plus-circle"></i>
                បន្ថែមសិស្ស
            </a>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto border border-gray-200 " >
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700">ល.រ</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700">ឈ្មោះ</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700">អ៊ីមែល</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-700">ថ្នាក់</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-700">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-gray-700">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $student->user->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $student->user->email ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $student->grade }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @if(Auth::user()->hasRole('teacher'))
                                        <a href="{{ route('subject.create', $student->id) }}" 
                                           class="text-blue-600 font-medium hover:underline mr2">
                                            បញ្ជូលពិន្ទុ
                                        </a>
                                    @endif

                                    @if(Auth::user()->hasRole('admin'))
                                        <a href="{{ route('students.edit', $student) }}" 
                                           class="text-green-600 font-medium hover:underline mr2">
                                           កែប្រែ
                                        </a>

                                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline"
                                              onsubmit="return confirm('តើអ្នកចង់លុបសិស្ស {{ $student->user->name }} មែនទេ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 font-medium hover:underline mr2">
                                                លុប
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                បញ្ជីឈ្មោះសិស្សថ្នាក់ {{ $className }} មិនមានទេ។
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Student Count --}}
        @if($students->count() > 0)
            <div class="mt-4 text-gray-600 text-sm">
                សរុប: <strong>{{ $students->count() }}</strong> នាក់
            </div>
        @endif
    </div>
</div>
@endsection
