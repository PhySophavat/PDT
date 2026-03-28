@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div>
        <p class="guest-kicker">PDT Portal</p>
        <h1 class="guest-title">ចូលប្រើប្រព័ន្ធ</h1>
        <p class="guest-subtitle">
            ប្រើអ៊ីមែល និងពាក្យសម្ងាត់របស់អ្នក ដើម្បីចូលទៅកាន់ផ្ទាំងគ្រប់គ្រង។
        </p>
    </div>

    @if (session('success'))
        <div class="guest-alert guest-alert--success">
            <i class="fa-solid fa-circle-check mt-1"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="guest-alert guest-alert--error">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="guest-alert guest-alert--error">
            <i class="fa-solid fa-circle-exclamation mt-1"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="guest-form">
        @csrf

        <div class="guest-field">
            <label for="email">អ៊ីមែល</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required>
        </div>

        <div class="guest-field">
            <label for="password">ពាក្យសម្ងាត់</label>
            <input id="password" type="password" name="password" placeholder="បញ្ចូលពាក្យសម្ងាត់" required>
        </div>

        <button type="submit" class="app-button app-button--primary w-full">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            <span>ចូលប្រើ</span>
        </button>
    </form>
@endsection
