@extends('layouts.app')

@section('title', 'កែសម្រួលសិទ្ធិអ្នកប្រើប្រាស់')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="bi bi-shield-check"></i> កែសម្រួលសិទ្ធិអ្នកប្រើប្រាស់
        </h1>
        <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> ត្រលប់ក្រោយ
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow border-0">
                <div class="card-header bg-blue-600 text-white">
                    <h5 class="mb-0">
                        គ្រប់គ្រងសិទ្ធិសម្រាប់: <strong>{{ $user->name }}</strong>
                      
                    </h5>
                </div>

                <div class="card-body bg-white">
                    <form method="POST" action="{{ route('user.permissions.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <p class="text-muted mb-4">
                            សូមជ្រើសរើសសិទ្ធិដែលអ្នកចង់ផ្តល់ឱ្យអ្នកប្រើប្រាស់នេះ
                        </p>

                        @if($permissions->isEmpty())
                            <div class="alert alert-warning">
                                មិនមានសិទ្ធិក្នុងប្រព័ន្ធទេ។
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($permissions as $permission)
                                    <div class="bg-gray-50 rounded-lg px-3 py-2 hover:bg-gray-100 transition-all border border-gray-200">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, $userPermissions ?? []) ? 'checked' : '' }}
                                                   class="form-check-input">
                                            <label for="perm_{{ $permission->id }}" class="form-check-label">
                                                <strong>{{ $permission->display_name }}</strong>
                                                @if($permission->description)
                                                    <br>
                                                    <small class="text-muted">{{ $permission->description }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 pt-3 border-t d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> រក្សាទុកការផ្លាស់ប្តូរ
                            </button>
                            <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">
                                បោះបង់
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection