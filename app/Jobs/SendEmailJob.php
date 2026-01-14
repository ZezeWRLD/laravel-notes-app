<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [60, 120, 300];

    public function __construct(
        public Email $email,
        public EmailTemplate $template,
        public array $data = []
    ) {}

    public function handle(): void
    {
        try {
            // Update email status to sending
            $this->email->update(['status' => 'sending']);

            // Render template with data
            $rendered = $this->template->render($this->data);

            // Create mailable
            $mailable = new \Illuminate\Mail\Mailable;
            $mailable->subject($rendered['subject'])
                     ->html($rendered['html'])
                     ->text($rendered['text'] ?? '');

            // Send email
            Mail::to($this->email->recipient_email)
                ->send($mailable);

            // Update email as sent
            $this->email->markAsSent();

            Log::info("Email sent successfully: {$this->email->id} to {$this->email->recipient_email}");

        } catch (\Exception $e) {
            Log::error("Failed to send email {$this->email->id}: " . $e->getMessage());

            $this->email->markAsFailed($e->getMessage());

            // Retry if applicable
            if ($this->email->canRetry()) {
                $this->release(60); // Retry after 1 minute
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Email job failed for email {$this->email->id}: " . $exception->getMessage());

        $this->email->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
