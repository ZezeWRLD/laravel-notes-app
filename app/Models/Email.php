<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'type',
        'subject',
        'html_content',
        'text_content',
        'template_slug',
        'message_id',
        'provider_id',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'open_count',
        'click_count',
        'status',
        'error_message',
        'retry_count',
        'last_retry_at',
        'metadata',
        'campaign_id',
        'ip_address',
        'user_agent',
        'related_note_id',
        'related_user_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'metadata' => 'array',
        'open_count' => 'integer',
        'click_count' => 'integer',
        'retry_count' => 'integer',
    ];

    protected $attributes = [
        'status' => 'pending',
        'open_count' => 0,
        'click_count' => 0,
        'retry_count' => 0,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedNote(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'related_note_id');
    }

    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_slug', 'slug');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecipient($query, $email)
    {
        return $query->where('recipient_email', $email);
    }

    // Methods
    public function markAsSent($messageId = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsOpened()
    {
        $this->increment('open_count');

        if (!$this->opened_at) {
            $this->update(['opened_at' => now()]);
        }
    }

    public function markAsClicked()
    {
        $this->increment('click_count');

        if (!$this->clicked_at) {
            $this->update(['clicked_at' => now()]);
        }
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
            'last_retry_at' => now(),
        ]);
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed'
            && $this->retry_count < 3
            && $this->last_retry_at < now()->subHours(1);
    }

    public function getMetadataAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = json_encode($value ?? []);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->status !== 'sent' && $this->status !== 'delivered') {
            return 0;
        }

        // For email marketing analytics
        return $this->open_count > 0 ? ($this->open_count / 1) * 100 : 0;
    }

    public function getClickRateAttribute(): float
    {
        if ($this->open_count === 0) {
            return 0;
        }

        return ($this->click_count / $this->open_count) * 100;
    }
}
