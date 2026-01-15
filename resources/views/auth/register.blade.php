@extends('layouts.auth')

@section('title', 'Register - Notes App')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('login') }}" class="font-medium text-note-primary hover:text-note-accent">
                    sign in to existing account
                </a>
            </p>
        </div>

        <!-- Form using tutorial-style components -->
        <x-forms.form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6">
            @csrf

            <x-forms.input
                label="Full Name"
                name="name"
                type="text"
                required
                autocomplete="name"
                autofocus
            />

            <x-forms.input
                label="Email address"
                name="email"
                type="email"
                required
                autocomplete="email"
            />

            <x-forms.input
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="new-password"
            />

            <x-forms.input
                label="Confirm Password"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
            />

            <div>
                <x-forms.button type="submit" class="w-full">
                    Create Account
                </x-forms.button>
            </div>
        </x-forms.form>
    </div>
</div>
@endsection
