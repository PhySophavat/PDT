@extends('layouts.app')

@section('content')
<div class="p-10 mt-4">
    <h1 class="font-bold text-2xl mb-5 text-gray-800">ការគ្រប់គ្រងតួនាទី (Role Management)</h1>

    <div class="row">
        @forelse($roles as $role)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-lg border-0 rounded-2xl overflow-hidden transition-all hover:shadow-xl">
                    
                    {{-- Card Header --}}
                    <div class="card-header bg-gray-100 d-flex justify-content-between align-items-center py-3">
                        <h1 class="mb-0 text-lg font-semibold">
                            <span class="badge px-3 py-2 text-white text-base rounded-lg shadow
                                @if($role->name === 'admin')
                                    bg-red-600
                                @elseif($role->name === 'teacher')
                                    bg-blue-600
                                @elseif($role->name === 'student')
                                    bg-green-600
                                @else
                                    bg-gray-600
                                @endif
                            ">
                                {{ $role->display_name }}
                            </span>
                        </h1>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body bg-white">
                        <p class="text-gray-600 mb-3">
                            {{ $role->description ?? 'មិនមានការពិពណ៌នា។' }}
                        </p>

                        <h6 class="font-semibold text-gray-700 mb-2">សិទ្ធិដែលត្រូវបានផ្ដល់:</h6>

                        <div class="mb-3">
                            @forelse($role->permissions as $permission)
                                <span class="badge bg-success text-white me-1 mb-1">
                                    <i class="bi bi-check-circle"></i> {{ $permission->display_name }}
                                </span>
                            @empty
                                <span class="badge bg-light text-dark">មិនទាន់មានសិទ្ធិផ្ដល់ទេ</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer bg-gray-50 border-t">
                        <a href="{{ route('roles.edit', $role->id) }}" 
                           class="btn btn-primary w-100 rounded-lg shadow-sm hover:bg-blue-700">
                            <i class="bi bi-gear"></i> កែសម្រួលសិទ្ធិ
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-10 w-full">
                មិនមានតួនាទី។
            </div>
        @endforelse
    </div>
</div>
@endsection
