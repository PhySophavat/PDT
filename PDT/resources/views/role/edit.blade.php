@extends('layouts.app')

@section('title', 'កែសម្រួលសិទ្ធិតួនាទី')

@section('content')
<div class="d-flex pt-10 justify-content-between align-items-center mb-4">
    <h1 class="font-bold text-2xl text-gray-800">
        <i class="bi bi-gear"></i> កែសម្រួលសិទ្ធិតួនាទី
    </h1>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> ត្រលប់ទៅតួនាទី
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-lg border-0 rounded-2xl overflow-hidden">
            
            {{-- Header --}}
            <div class="card-header bg-gray-100 py-3">
                <h5 class="mb-0 text-gray-700">
                    ការគ្រប់គ្រងសិទ្ធិ សម្រាប់  
                    <span class="badge px-3 py-2 text-white text-base rounded-lg shadow
                        @if($role->name === 'admin') bg-red-600
                        @elseif($role->name === 'teacher') bg-blue-600
                        @elseif($role->name === 'student') bg-green-600
                        @else bg-gray-600
                        @endif
                    ">
                        {{ $role->display_name }}
                    </span>
                </h5>
            </div>

            {{-- Body --}}
            <div class="card-body bg-white">
                <form method="POST" action="{{ route('roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')

                    <p class="text-muted mb-4">
                        {{ $role->description ?? 'មិនមានការពិពណ៌នា។' }}
                    </p>

                    {{-- 3-column responsive grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                            <div class="bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100 transition-all border border-gray-200">
                                <div class="form-check">
                                    <input class="form-check-input me-2"
                                           type="checkbox" 
                                           name="permissions[]" 
                                           id="permission_{{ $permission->id }}"
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, $rolePermissions ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label text-gray-700" for="permission_{{ $permission->id }}">
                                        <strong>{{ $permission->display_name }}</strong>
                                        @if($permission->description)
                                            <br>
                                            <small class="text-gray-500">{{ $permission->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer --}}
                    <div class="mt-5 border-t pt-3 flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> ធ្វើបច្ចុប្បន្នភាពសិទ្ធិ
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                            បោះបង់
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
