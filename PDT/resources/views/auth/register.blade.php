@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div>
        <p class="guest-kicker">PDT Portal</p>
        <h1 class="guest-title">បង្កើតគណនីថ្មី</h1>
        <p class="guest-subtitle">
            ទំព័រនេះត្រូវបានរៀបចំរួចសម្រាប់ dark mode ប៉ុន្តែមុខងារចុះឈ្មោះមិនទាន់ត្រូវបានកំណត់ route នៅក្នុងប្រព័ន្ធនេះនៅឡើយទេ។
        </p>
    </div>

    <form class="guest-form" action="{{ Route::has('register') ? route('register') : '#' }}" method="POST">
        @csrf

        <div class="guest-field">
            <label for="register_name">Name</label>
            <input id="register_name" type="text" name="name" placeholder="Enter your name">
        </div>

        <div class="guest-field">
            <label for="register_email">Email address</label>
            <input id="register_email" type="email" name="email" placeholder="example@email.com">
        </div>

        <div class="guest-field">
            <label for="register_password">Password</label>
            <input id="register_password" type="password" name="password" placeholder="Create a password">
        </div>

        <div class="guest-field">
            <label for="register_password_confirmation">Confirm Password</label>
            <input id="register_password_confirmation" type="password" name="password_confirmation" placeholder="Confirm your password">
        </div>

        <button type="submit" class="app-button app-button--primary w-full" {{ Route::has('register') ? '' : 'disabled' }}>
            <i class="fa-solid fa-user-plus"></i>
            <span>Register</span>
        </button>
    </form>

    <p class="guest-footer">
        មានគណនីរួចហើយ? <a href="{{ route('login') }}">Login here</a>
    </p>
@endsection
