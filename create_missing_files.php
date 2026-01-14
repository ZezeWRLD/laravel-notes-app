<?php
// Create missing Laravel files

$basePath = __DIR__;

echo "Creating missing Laravel files...\n";

// 1. Create Providers directory
$providersDir = $basePath . '/app/Providers';
if (!is_dir($providersDir)) {
    mkdir($providersDir, 0755, true);
}

// 2. Create the essential providers
$providers = [
    'AppServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}',

    'AuthServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}',

    'EventServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}',

    'RouteServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = "/home";

    public function boot(): void
    {
        RateLimiter::for("api", function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware("api")
                ->prefix("api")
                ->group(base_path("routes/api.php"));

            Route::middleware("web")
                ->group(base_path("routes/web.php"));
        });
    }
}',

    'EmailServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}',
];

foreach ($providers as $filename => $content) {
    file_put_contents($providersDir . '/' . $filename, $content);
    echo "✓ Created: $filename\n";
}

// 3. Create Models directory and basic models
$modelsDir = $basePath . '/app/Models';
if (!is_dir($modelsDir)) {
    mkdir($modelsDir, 0755, true);
}

// 4. Create Services directory
$servicesDir = $basePath . '/app/Services';
if (!is_dir($servicesDir)) {
    mkdir($servicesDir, 0755, true);

    // Create EmailService
    $emailService = '<?php

namespace App\Services;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function sendNoteShared(
        User $sender,
        User $recipient,
        \App\Models\Note $note,
        string $accessLevel
    ): ?Email {
        try {
            Log::info("Note shared email would be sent to: " . $recipient->email);
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            return null;
        }
    }
}';

    file_put_contents($servicesDir . '/EmailService.php', $emailService);
    echo "✓ Created: EmailService.php\n";
}

// 5. Clear cache
$cacheDir = $basePath . '/bootstrap/cache';
if (is_dir($cacheDir)) {
    array_map("unlink", glob($cacheDir . "/*"));
    echo "✓ Cache cleared\n";
}

echo "\n✅ Missing files created! Try running: php artisan --version\n";
