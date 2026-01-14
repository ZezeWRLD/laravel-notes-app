<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function settings()
    {
        return view('email.settings');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications_enabled' => 'nullable|boolean',
            'preferences' => 'nullable|array',
            'preferences.*' => 'boolean',
        ]);

        // Update main email setting
        if ($request->has('email_notifications_enabled')) {
            $user->email_notifications_enabled = $request->email_notifications_enabled;
        }

        // Update preferences
        if ($request->has('preferences')) {
            foreach ($request->preferences as $type => $value) {
                $user->updateEmailPreference($type, $value);
            }
        }

        $user->save();

        return redirect()->route('email.settings')
            ->with('success', 'Email settings updated successfully.');
    }

    public function unsubscribe($token)
    {
        $user = User::where('unsubscribe_token', $token)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Invalid unsubscribe link.');
        }

        $user->email_notifications_enabled = false;
        $user->save();

        // Log the unsubscribe
        Email::create([
            'recipient_email' => $user->email,
            'type' => 'system_alert',
            'subject' => 'User unsubscribed from emails',
            'metadata' => [
                'action' => 'unsubscribe',
                'user_id' => $user->id,
                'timestamp' => now()->toISOString(),
            ],
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return view('email.unsubscribed', compact('user'));
    }

    public function subscribe($token)
    {
        $user = User::where('unsubscribe_token', $token)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Invalid subscription link.');
        }

        $user->email_notifications_enabled = true;
        $user->save();

        return redirect()->route('email.settings')
            ->with('success', 'You have been resubscribed to email notifications.');
    }

    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }

        if ($user->has_verified_email) {
            return redirect('/')->with('info', 'Email already verified.');
        }

        $user->verifyEmail();

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Email verified successfully!');
    }

    public function trackOpen($emailId)
    {
        $email = Email::findOrFail($emailId);

        $this->emailService->trackEmailOpen($email, request());

        // Return a 1x1 transparent pixel
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return response($pixel)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Cache-Control', 'post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache');
    }

    public function trackClick($emailId)
    {
        $email = Email::findOrFail($emailId);
        $url = request()->query('url');

        if (!$url) {
            abort(400, 'Missing URL parameter');
        }

        $this->emailService->trackEmailClick($email, $url, request());

        return redirect($url);
    }

    public function preview($templateSlug)
    {
        if (!app()->environment('local') && !Auth::check()) {
            abort(403, 'Email preview only available in local environment');
        }

        $template = EmailTemplate::where('slug', $templateSlug)->firstOrFail();

        $data = request()->query('data')
            ? json_decode(request()->query('data'), true)
            : [];

        // Add default data for preview
        $defaultData = [
            'sender_name' => 'John Doe',
            'recipient_name' => 'Jane Smith',
            'note_title' => 'Sample Note Title',
            'note_excerpt' => 'This is a sample note excerpt for preview purposes...',
            'note_url' => url('/notes/1'),
            'access_level' => 'Edit',
            'unsubscribe_url' => url('/email/unsubscribe/token'),
            'settings_url' => url('/email/settings'),
            'app_name' => config('app.name'),
            'year' => date('Y'),
            'user_name' => 'Jane Smith',
            'dashboard_url' => url('/dashboard'),
            'verification_url' => url('/email/verify/token'),
            'help_url' => url('/help'),
            'contact_email' => config('mail.from.address'),
            'collaborator_name' => 'Jane Smith',
            'inviter_name' => 'John Doe',
            'expires_at' => now()->addDays(7)->format('F j, Y \a\t g:i A'),
            'accept_url' => url('/invitations/accept/token'),
            'decline_url' => url('/invitations/decline/token'),
            'date' => now()->format('F j, Y'),
            'new_notes_count' => 3,
            'updated_notes_count' => 5,
            'new_comments_count' => 2,
            'activity_url' => url('/activity'),
        ];

        $mergedData = array_merge($defaultData, $data);

        $rendered = $template->render($mergedData);

        return response($rendered['html']);
    }

    public function analytics()
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $stats = [
            'total_emails' => Email::count(),
            'sent_today' => Email::whereDate('sent_at', today())->count(),
            'delivery_rate' => Email::where('status', 'delivered')->count() / max(Email::whereIn('status', ['sent', 'delivered'])->count(), 1) * 100,
            'open_rate' => Email::where('open_count', '>', 0)->count() / max(Email::where('status', 'delivered')->count(), 1) * 100,
            'click_rate' => Email::where('click_count', '>', 0)->count() / max(Email::where('status', 'delivered')->count(), 1) * 100,
        ];

        $recentEmails = Email::with(['user', 'relatedNote'])
            ->latest()
            ->limit(20)
            ->get();

        return view('email.analytics', compact('stats', 'recentEmails'));
    }
}
