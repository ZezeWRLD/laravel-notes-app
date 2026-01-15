@extends('layouts.auth')

@section('title', 'Login - Notes App')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('register') }}" class="font-medium text-note-primary hover:text-note-accent">
                    create a new account
                </a>
            </p>
        </div>

        <!-- Form using tutorial-style components -->
        <x-forms.form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
            @csrf

            <x-forms.input
                label="Email address"
                name="email"
                type="email"
                required
                autocomplete="email"
                autofocus
            />

            <x-forms.input
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
            />

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-4 w-4 text-note-primary focus:ring-note-primary border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>
            </div>

            <div>
                <x-forms.button type="submit" class="w-full">
                    Sign in
                </x-forms.button>
            </div>
        </x-forms.form>
    </div>
</div>
@endsection
