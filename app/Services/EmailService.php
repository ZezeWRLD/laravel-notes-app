<?php

namespace App\Services;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send a template-based email
     */
    public function sendTemplateEmail(
        User $recipient,
        string $templateSlug,
        array $data = [],
        array $metadata = []
    ): ?Email {
        try {
            // Check if user should receive emails
            if (!$recipient->shouldReceiveEmail($templateSlug)) {
                Log::info("User {$recipient->email} opted out of {$templateSlug} emails");
                return null;
            }

            // Check rate limiting
            if (!$recipient->canSendEmail($templateSlug)) {
                Log::info("Rate limit reached for user {$recipient->email} for {$templateSlug}");
                return null;
            }

            // Get template
            $template = EmailTemplate::where('slug', $templateSlug)->first();

            if (!$template) {
                Log::error("Email template {$templateSlug} not found");
                return null;
            }

            // Create email record
            $email = Email::create([
                'user_id' => $recipient->id,
                'recipient_email' => $recipient->email,
                'recipient_name' => $recipient->name,
                'type' => $templateSlug,
                'template_slug' => $templateSlug,
                'subject' => $template->subject,
                'metadata' => $metadata,
                'status' => 'queued',
            ]);

            // Queue the email
            \App\Jobs\SendEmailJob::dispatch($email, $template, $data);

            // Increment template usage
            $template->increment('usage_count');
            $template->update(['last_used_at' => now()]);

            // Update user's last email sent time
            $recipient->updateLastEmailSent();

            return $email;

        } catch (\Exception $e) {
            Log::error("Failed to queue email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send note shared notification
     */
    public function sendNoteShared(
        User $sender,
        User $recipient,
        \App\Models\Note $note,
        string $accessLevel
    ): ?Email {
        return $this->sendTemplateEmail($recipient, 'note_shared', [
            'sender_name' => $sender->name,
            'recipient_name' => $recipient->name,
            'note_title' => $note->title,
            'note_excerpt' => substr($note->content ?? '', 0, 150),
            'note_url' => route('notes.show', $note),
            'access_level' => ucfirst($accessLevel),
            'unsubscribe_url' => $recipient->getUnsubscribeUrl(),
            'settings_url' => route('profile.email-settings'),
            'app_name' => config('app.name'),
            'year' => date('Y'),
        ], [
            'note_id' => $note->id,
            'sender_id' => $sender->id,
            'access_level' => $accessLevel,
        ]);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(User $user): ?Email
    {
        return $this->sendTemplateEmail($user, 'welcome', [
            'user_name' => $user->name,
            'app_name' => config('app.name'),
            'dashboard_url' => route('dashboard'),
            'verification_url' => $user->getVerificationUrl(),
            'help_url' => route('help'),
            'contact_email' => config('mail.from.address'),
            'year' => date('Y'),
        ]);
    }

    /**
     * Send email verification
     */
    public function sendVerificationEmail(User $user): ?Email
    {
        $token = $user->generateVerificationToken();

        return $this->sendTemplateEmail($user, 'email_verification', [
            'user_name' => $user->name,
            'app_name' => config('app.name'),
            'verification_url' => $user->getVerificationUrl(),
            'expiry_hours' => 24,
            'year' => date('Y'),
        ]);
    }

    /**
     * Track email opens
     */
    public function trackEmailOpen(Email $email, $request): void
    {
        $email->increment('open_count');

        if (!$email->opened_at) {
            $email->update(['opened_at' => now()]);
        }

        $email->update([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
