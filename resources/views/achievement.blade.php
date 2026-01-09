<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievement Unlocked! - Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="gradient-bg min-h-screen p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-block bg-yellow-400 text-yellow-900 text-2xl md:text-4xl font-bold px-6 py-3 rounded-full mb-4 animate-pulse">
                🏆 ACHIEVEMENT UNLOCKED
            </div>
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Custom MVC → Laravel</h1>
            <p class="text-xl text-white/80">From building frameworks to using professional tools</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-8">
            <div class="md:flex">
                <!-- Left Column - Your Custom MVC -->
                <div class="md:w-1/2 p-8 md:p-12 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 p-3 rounded-xl mr-4">
                            <span class="text-3xl">🛠️</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">What You Built From Scratch</h2>
                            <p class="text-gray-600">Your Custom PHP MVC Framework</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="card-hover bg-white p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <span class="text-green-600">✅</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">config.php</h3>
                                    <p class="text-sm text-gray-600">Configuration, autoloader, helpers</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <span class="text-green-600">✅</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Custom Router</h3>
                                    <p class="text-sm text-gray-600">routes.php with controller mapping</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <span class="text-green-600">✅</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Database Layer</h3>
                                    <p class="text-sm text-gray-600">PDO wrapper with prepared statements</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <span class="text-green-600">✅</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Full CRUD Operations</h3>
                                    <p class="text-sm text-gray-600">Create, Read, Update, Delete for notes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Laravel Benefits -->
                <div class="md:w-1/2 p-8 md:p-12 bg-gradient-to-br from-purple-50 to-pink-50">
                    <div class="flex items-center mb-6">
                        <div class="bg-purple-100 p-3 rounded-xl mr-4">
                            <span class="text-3xl">🚀</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">What Laravel Gives You</h2>
                            <p class="text-gray-600">Professional PHP Framework</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="card-hover bg-white p-4 rounded-xl border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <span class="text-purple-600">⚡</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Artisan CLI</h3>
                                    <p class="text-sm text-gray-600">Code generation & automation</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <span class="text-purple-600">💎</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Eloquent ORM</h3>
                                    <p class="text-sm text-gray-600">Beautiful, expressive database queries</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <span class="text-purple-600">🛡️</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Built-in Security</h3>
                                    <p class="text-sm text-gray-600">CSRF protection, validation, auth</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-hover bg-white p-4 rounded-xl border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <span class="text-purple-600">📦</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Package Ecosystem</h3>
                                    <p class="text-sm text-gray-600">Thousands of community packages</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="p-8 bg-gradient-to-r from-yellow-50 to-orange-50 border-t">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">🎓 Understanding Fundamentals = Better Developer</h3>
                    <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                        By building a custom MVC framework, you now understand what happens under the hood in Laravel.
                        This makes you a better developer because you know the "why" behind Laravel's design choices.
                    </p>

                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="/"
                           class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            🏠 Laravel Home
                        </a>
                        <a href="/compare"
                           class="px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                            🔄 Feature Comparison
                        </a>
                        <a href="https://laravel.com/docs" target="_blank"
                           class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                            📚 Laravel Docs
                        </a>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-gray-500 text-sm">
                            <strong>Time Investment:</strong> Weeks building custom MVC → Days learning Laravel → Hours building features
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
