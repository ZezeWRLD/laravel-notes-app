<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite assets (Exactly like tutorial) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation (Like tutorial auth conditionals) -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('notes.index') }}" class="text-xl font-bold text-note-primary">
                            📝 Notes App
                        </a>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('notes.index') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700
                                  inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            My Notes
                        </a>
                        <a href="{{ route('notes.create') }}"
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700
                                  inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Create Note
                        </a>
                    </div>
                </div>

                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <!-- Auth Navigation (Exactly like tutorial @auth/@guest) -->
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                Welcome, {{ auth()->user()->name }}
                            </span>

                            <!-- Logout form (Exactly like tutorial) -->
                            <x-forms.form method="DELETE" action="{{ route('logout') }}" class="inline">
                                <x-forms.button type="submit" class="text-sm">
                                    Log Out
                                </x-forms.button>
                            </x-forms.form>
                        </div>
                    @endauth

                    @guest
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-gray-500 hover:text-gray-900">
                                Log in
                            </a>
                            <a href="{{ route('register') }}"
                               class="text-sm font-medium text-white bg-note-primary hover:bg-note-accent
                                      px-4 py-2 rounded-md transition">
                                Sign up
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
