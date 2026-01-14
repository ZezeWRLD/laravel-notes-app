<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_notifications_enabled',
        'email_verified_at',
        'unsubscribe_token',
        'last_email_sent_at',
        'email_verification_token',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
             'email_notifications_enabled' => 'boolean',
            'email_verified_at' => 'datetime',
            'last_email_sent_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }
    // Relationships
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function sentEmails(): HasMany
    {
        return $this->hasMany(Email::class, 'related_user_id');
    }

    // Accessors
    public function getHasVerifiedEmailAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }

     public function getNotificationPreferencesAttribute($value)
    {
        $defaults = [
            'note_shared' => true,
            'collaborator_invitation' => true,
            'note_updated' => true,
            'note_commented' => true,
            'daily_digest' => false,
            'weekly_summary' => false,
            'marketing' => false,
        ];

        $preferences = json_decode($value, true) ?? [];

        return array_merge($defaults, $preferences);
    }

    public function setNotificationPreferencesAttribute($value)
    {
        $this->attributes['notification_preferences'] = json_encode($value ?? []);
    }

    public function getEmailPreference($type): bool
    {
        $preferences = $this->notification_preferences;

        return $preferences[$type] ?? false;
    }

    public function updateEmailPreference($type, $value): void
    {
        $preferences = $this->notification_preferences;
        $preferences[$type] = (bool) $value;

        $this->notification_preferences = $preferences;
        $this->save();
    }

    // Methods
    public function shouldReceiveEmail($type): bool
    {
        if (!$this->email_notifications_enabled || !$this->has_verified_email) {
            return false;
        }

        return $this->getEmailPreference($type);
    }

    public function generateUnsubscribeToken(): string
    {
        $this->unsubscribe_token = hash('sha256', Str::random(60) . time() . $this->email);
        $this->save();

        return $this->unsubscribe_token;
    }

    public function generateVerificationToken(): string
    {
        $this->email_verification_token = hash('sha256', Str::random(60) . time());
        $this->save();

        return $this->email_verification_token;
    }

    public function verifyEmail(): void
    {
        $this->email_verified_at = now();
        $this->email_verification_token = null;
        $this->save();
    }

    public function updateLastEmailSent(): void
    {
        $this->last_email_sent_at = now();
        $this->save();
    }

    public function getUnsubscribeUrl(): string
    {
        return url('/email/unsubscribe/' . $this->unsubscribe_token);
    }

    public function getVerificationUrl(): string
    {
        return url('/email/verify/' . $this->email_verification_token);
    }

    // Email frequency limiting
    public function canSendEmail($type = null): bool
    {
        // If no email sent yet, allow
        if (!$this->last_email_sent_at) {
            return true;
        }

        // Check rate limiting based on type
        $now = now();
        $lastSent = $this->last_email_sent_at;

        switch ($type) {
            case 'marketing':
                // Max 1 marketing email per day
                return $lastSent->diffInHours($now) >= 24;
            case 'daily_digest':
                // Max 1 daily digest per day
                return $lastSent->diffInHours($now) >= 24;
            case 'notification':
                // Max 1 notification per hour
                return $lastSent->diffInMinutes($now) >= 60;
            default:
                // Default: max 1 email per 10 minutes
                return $lastSent->diffInMinutes($now) >= 10;
        }
    }


    /**
     * Get the notes for the user.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
