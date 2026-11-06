@extends('layouts.guest')

@section('title', 'ចូលប្រើប្រព័ន្ធ | portal sala')

@section('content')
<div class="bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-3xl p-7 sm:p-9 shadow-xl shadow-slate-200/60 space-y-6">

    <!-- Card Top Header -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            <span>PORTAL LOGIN • portal sala</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            ចូលប្រើប្រព័ន្ធ
        </h1>
        <p class="text-xs text-slate-500 max-w-xs mx-auto">
            សូមបញ្ចូលអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក ដើម្បីចូលទៅកាន់ផ្ទាំងគ្រប់គ្រងសាលារៀន។
        </p>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="flex items-start gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-2.5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-2.5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm mt-0.5 flex-shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- Login Form -->
    <form action="/login" method="POST" class="space-y-4">
        @csrf

        <!-- Email Field -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold text-slate-700">
                <span>អាសយដ្ឋានអ៊ីមែល</span>
                <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-regular fa-envelope text-sm"></i>
                </span>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="name@school.edu.kh" 
                    required
                    autofocus
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
                >
            </div>
        </div>

        <!-- Password Field with Show/Hide Toggle -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-bold text-slate-700">
                    <span>ពាក្យសម្ងាត់</span>
                    <span class="text-rose-500">*</span>
                </label>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="••••••••" 
                    value=""
                    required
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition"
                >
                <button 
                    type="button" 
                    id="toggle-password" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                    title="បង្ហាញ/លាក់ពាក្យសម្ងាត់"
                >
                    <i class="fa-regular fa-eye text-sm" id="toggle-icon"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1 text-xs">
            <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-0 cursor-pointer">
                <span>ចងចាំគណនីខ្ញុំ</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full py-3 px-5 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all hover:shadow-lg hover:shadow-blue-500/35 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer"
        >
            <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
            <span>ចូលប្រើប្រព័ន្ធ</span>
        </button>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Password Visibility Toggle
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-icon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'fa-regular fa-eye-slash text-sm text-blue-600' : 'fa-regular fa-eye text-sm';
        });
    }
</script>
@endpush
