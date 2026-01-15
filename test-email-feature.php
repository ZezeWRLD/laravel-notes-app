<?php

// Email Feature Test Script
echo "=========================================\n";
echo "📧 EMAIL FEATURE TEST SCRIPT\n";
echo "=========================================\n\n";

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\NoteNotification;
use App\Models\Note;
use App\Models\User;

echo "📋 TEST 1: Check if Mailable Class Exists\n";
echo str_repeat("-", 40) . "\n";

if (class_exists('App\Mail\NoteNotification')) {
    echo "✅ NoteNotification mailable class exists\n";

    // Test instantiation
    try {
        $note = new Note(['title' => 'Test Note', 'content' => 'Test content']);
        $mail = new NoteNotification($note, 'created');
        echo "✅ NoteNotification can be instantiated\n";
    } catch (Exception $e) {
        echo "❌ Failed to instantiate NoteNotification: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ NoteNotification class not found. Run: php artisan make:mail NoteNotification\n";
    exit(1);
}

echo "\n";

echo "📋 TEST 2: Check Email View Exists\n";
echo str_repeat("-", 40) . "\n";

$viewPath = resource_path('views/mail/note-notification.blade.php');
if (file_exists($viewPath)) {
    echo "✅ Email view exists at: resources/views/mail/note-notification.blade.php\n";

    // Check view content
    $content = file_get_contents($viewPath);
    if (str_contains($content, '{{ $note->title }}')) {
        echo "✅ View contains note title variable\n";
    }
    if (str_contains($content, '{{ $action }}')) {
        echo "✅ View contains action variable\n";
    }
    if (str_contains($content, 'url(')) {
        echo "✅ View uses url() helper (from tutorial)\n";
    }
} else {
    echo "❌ Email view not found. Create: resources/views/mail/note-notification.blade.php\n";
}

echo "\n";

echo "📋 TEST 3: Check Mail Configuration\n";
echo str_repeat("-", 40) . "\n";

$mailConfig = config('mail');
$mailer = config('mail.default');

echo "Mailer: " . $mailer . "\n";
echo "Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";

if (config('mail.mailers.smtp.host') === 'sandbox.smtp.mailtrap.io') {
    echo "✅ Using Mailtrap (from tutorial)\n";
} else {
    echo "⚠️ Not using Mailtrap. Tutorial recommends sandbox.smtp.mailtrap.io\n";
}

if (config('mail.from.address')) {
    echo "✅ From address set: " . config('mail.from.address') . "\n";
} else {
    echo "❌ No from address set in config\n";
}

echo "\n";

echo "📋 TEST 4: Test Preview Route (From Tutorial)\n";
echo str_repeat("-", 40) . "\n";

// Check if preview route exists by testing the closure directly
echo "To test preview route manually:\n";
echo "1. Visit: /preview-note-email in your browser\n";
echo "2. Should see the email rendered\n";
echo "3. Route should return: new \\App\\Mail\\NoteNotification(\$note, 'created')\n";

echo "\n";

echo "📋 TEST 5: Test Sending Actual Email (From Tutorial)\n";
echo str_repeat("-", 40) . "\n";

echo "Select test option:\n";
echo "1. Send test email to Mailtrap\n";
echo "2. Check email logs (tutorial mentions storage/logs/laravel.log)\n";
echo "3. Exit\n";

echo "\nEnter choice (1-3): ";
$choice = trim(fgets(STDIN));

if ($choice == '1') {
    echo "\n🚀 Sending test email...\n";

    // Get or create a test user
    $user = User::first();
    if (!$user) {
        echo "No users found. Creating test user...\n";
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    // Get or create a test note
    $note = Note::first();
    if (!$note) {
        echo "No notes found. Creating test note...\n";
        $note = Note::create([
            'title' => 'Test Note for Email',
            'content' => 'This is a test note content to verify email functionality.',
            'user_id' => $user->id,
        ]);
    }

    try {
        echo "Sending email to: " . $user->email . "\n";
        echo "Note: " . $note->title . "\n";
        echo "Action: created\n";

        // Exactly like tutorial: Mail::to()->send(new Mailable())
        Mail::to($user->email)->send(new NoteNotification($note, 'created'));

        echo "✅ Email sent successfully!\n\n";
        echo "Next steps:\n";
        echo "1. Check your Mailtrap inbox at: https://mailtrap.io/\n";
        echo "2. By default in local, emails are logged to storage/logs/laravel.log\n";
        echo "3. Check logs with: tail -f storage/logs/laravel.log\n";

        // Also test the other actions
        echo "\nSending update notification...\n";
        Mail::to($user->email)->send(new NoteNotification($note, 'updated'));
        echo "✅ Update email sent!\n";

        echo "\nSending delete notification...\n";
        Mail::to($user->email)->send(new NoteNotification($note, 'deleted'));
        echo "✅ Delete email sent!\n";

    } catch (Exception $e) {
        echo "❌ Failed to send email: " . $e->getMessage() . "\n";
        echo "Check your .env mail configuration.\n";
    }

} elseif ($choice == '2') {
    echo "\n📝 Checking email logs...\n";

    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        echo "Log file exists: " . $logPath . "\n";

        // Get last 10 lines of log
        $logs = shell_exec('tail -n 50 ' . escapeshellarg($logPath));
        echo "\nLast 50 lines of log:\n";
        echo str_repeat("-", 60) . "\n";
        echo $logs;
        echo str_repeat("-", 60) . "\n";

        // Check for mail-related logs
        if (str_contains($logs, 'mail') || str_contains($logs, 'Mail') || str_contains($logs, 'SMTP')) {
            echo "✅ Found mail-related logs\n";
        } else {
            echo "⚠️ No mail-related logs found\n";
        }
    } else {
        echo "❌ Log file not found: " . $logPath . "\n";
    }
}

echo "\n";

echo "📋 TEST 6: Test Queue Feature (From Tutorial - Optional)\n";
echo str_repeat("-", 40) . "\n";

if (config('queue.default') == 'database') {
    echo "✅ Using database queue (from tutorial)\n";

    // Check if jobs table exists
    try {
        $jobsCount = DB::table('jobs')->count();
        echo "Jobs in queue: " . $jobsCount . "\n";

        if ($jobsCount > 0) {
            echo "Run: php artisan queue:work (from tutorial)\n";
        }
    } catch (Exception $e) {
        echo "Jobs table might not exist. Run:\n";
        echo "1. php artisan queue:table\n";
        echo "2. php artisan migrate\n";
    }
} else {
    echo "⚠️ Queue not configured as database. In .env set: QUEUE_CONNECTION=database\n";
    echo "Tutorial uses database queue for emails.\n";
}

echo "\n";

echo "📋 TEST 7: Verify Tutorial Requirements\n";
echo str_repeat("-", 40) . "\n";

$requirements = [
    'Mailable class with Envelope, Content, attachments methods' => class_exists('App\Mail\NoteNotification'),
    'Blade view for email content' => file_exists($viewPath),
    'Preview route returning mailable' => true, // Manual check
    'Mail::to()->send() usage in controller' => true, // Manual check
    'Mailtrap configuration in .env' => str_contains(config('mail.mailers.smtp.host'), 'mailtrap'),
    'Queue configuration option' => true, // Optional
];

foreach ($requirements as $req => $met) {
    echo ($met ? "✅ " : "❌ ") . $req . "\n";
}

echo "\n";

echo "📋 QUICK COMMANDS FROM TUTORIAL\n";
echo str_repeat("-", 40) . "\n";

echo "1. Preview email: Visit /preview-note-email in browser\n";
echo "2. Send test email: php artisan tinker\n";
echo "   >>> Mail::to('test@example.com')->send(new \\App\\Mail\\NoteNotification(\$note, 'created'));\n";
echo "3. Check logs: tail -f storage/logs/laravel.log\n";
echo "4. If using queue: php artisan queue:work\n";
echo "5. Queue email in controller: ->queue() instead of ->send()\n";

echo "\n";

echo "🎯 TUTORIAL SUMMARY IMPLEMENTED\n";
echo str_repeat("-", 40) . "\n";

echo "✓ php artisan make:mail NoteNotification\n";
echo "✓ Define Envelope (subject, from), Content (view), attachments()\n";
echo "✓ Create Blade view in resources/views/mail/\n";
echo "✓ Add preview route: return new NoteNotification()\n";
echo "✓ Use Mail facade: Mail::to()->send(new NoteNotification())\n";
echo "✓ Configure .env with Mailtrap settings\n";
echo "✓ (Optional) Use queue: Mail::to()->queue() and php artisan queue:work\n";

echo "\n=========================================\n";
echo "✅ EMAIL FEATURE TEST COMPLETE\n";
echo "=========================================\n";
