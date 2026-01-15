<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('notes.index') }}" class="text-xl font-bold text-blue-600">
                            📝 Notes App
                        </a>
                    </div>

                    @auth
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
                    @endauth
                </div>

                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">
                                Welcome, {{ auth()->user()->name }}
                            </span>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-900">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    @endauth

                    @guest
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-gray-500 hover:text-gray-900">
                                Log in
                            </a>
                            <a href="{{ route('register') }}"
                               class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700
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
