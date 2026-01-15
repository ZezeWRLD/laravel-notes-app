<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Note;

echo "==========================================\n";
echo "PHASE 2: COMPREHENSIVE FEATURE TESTING\n";
echo "==========================================\n\n";

// TEST 1: DATABASE STATE
echo "=== 1. DATABASE STATE TEST ===\n";
$users = DB::table('users')->count();
$notes = DB::table('notes')->count();
$tags = DB::table('tags')->count();
echo "✓ Users: $users\n";
echo "✓ Notes: $notes\n";
echo "✓ Tags: $tags\n\n";

// TEST 2: USER-NOTE RELATIONSHIP
echo "=== 2. USER-NOTE RELATIONSHIP TEST ===\n";
if ($users > 0) {
    $user = User::first();
    $userNotes = $user->notes()->count();
    echo "✓ Sample User: {$user->name} ({$user->email})\n";
    echo "✓ Notes owned by user: $userNotes\n";
} else {
    echo "⚠ No users in database\n";
}
echo "\n";

// TEST 3: ROUTE AVAILABILITY
echo "=== 3. ROUTE AVAILABILITY TEST ===\n";
$routes = [
    'login' => 'GET /login',
    'register' => 'GET /register',
    'dashboard' => 'GET /dashboard',
    'notes.index' => 'GET /notes',
    'notes.create' => 'GET /notes/create',
    'notes.store' => 'POST /notes',
    'search' => 'GET /search',
    'tags.show' => 'GET /tags/{tag}',
];

$routeCollection = Route::getRoutes();
$availableRoutes = [];
foreach ($routeCollection as $route) {
    $availableRoutes[$route->getName()] = $route;
}

foreach ($routes as $name => $description) {
    if (isset($availableRoutes[$name])) {
        echo "✓ $name: Available\n";
    } else {
        echo "✗ $name: MISSING\n";
    }
}
echo "\n";

// TEST 4: MODELS & MIGRATIONS
echo "=== 4. MODELS & SCHEMA TEST ===\n";
$tables = [
    'users' => ['id', 'name', 'email', 'password'],
    'notes' => ['id', 'title', 'content', 'user_id', 'featured'],
    'tags' => ['id', 'name', 'slug'],
];

foreach ($tables as $table => $columns) {
    $tableExists = DB::getSchemaBuilder()->hasTable($table);
    echo "✓ Table '$table': " . ($tableExists ? 'EXISTS' : 'MISSING') . "\n";

    if ($tableExists) {
        $schemaColumns = DB::getSchemaBuilder()->getColumnListing($table);
        foreach ($columns as $col) {
            $exists = in_array($col, $schemaColumns);
            echo "  " . ($exists ? '✓' : '✗') . " Column '$col'\n";
        }
    }
}
echo "\n";

// TEST 5: AUTHENTICATION GUARDS
echo "=== 5. AUTHENTICATION SETUP TEST ===\n";
$guards = config('auth.guards');
echo "✓ Available guards: " . implode(', ', array_keys($guards)) . "\n";
$defaultGuard = config('auth.defaults.guard');
echo "✓ Default guard: $defaultGuard\n\n";

// TEST 6: MAIL CONFIGURATION
echo "=== 6. MAIL CONFIGURATION TEST ===\n";
$mailDriver = config('mail.default');
$mailHost = config('mail.mailers.smtp.host');
echo "✓ Mail driver: $mailDriver\n";
echo "✓ SMTP Host: $mailHost\n";
echo "✓ Mail from address: " . config('mail.from.address') . "\n\n";

// TEST 7: COMPONENT CHECK
echo "=== 7. VIEW COMPONENTS TEST ===\n";
$components = [
    'AppLayout' => \App\View\Components\AppLayout::class,
    'GuestLayout' => \App\View\Components\GuestLayout::class,
];

foreach ($components as $name => $class) {
    $exists = class_exists($class);
    echo "✓ Component {$name}: " . ($exists ? 'LOADED' : 'MISSING') . "\n";
}
echo "\n";

echo "==========================================\n";
echo "PHASE 2 INITIAL DIAGNOSTICS: COMPLETE\n";
echo "==========================================\n";
