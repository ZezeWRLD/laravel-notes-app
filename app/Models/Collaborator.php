<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'user_id',
        'access_level',
        'invitation_sent_at',
        'invitation_token',
        'invitation_expires_at',
        'invitation_accepted',
        'invitation_accepted_at',
        'notification_preferences',
    ];

    protected $casts = [
        'invitation_sent_at' => 'datetime',
        'invitation_expires_at' => 'datetime',
        'invitation_accepted_at' => 'datetime',
        'invitation_accepted' => 'boolean',
        'notification_preferences' => 'array',
    ];

    // Relationships
    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function generateInvitationToken(): string
    {
        $this->invitation_token = hash('sha256', Str::random(60) . time());
        $this->invitation_expires_at = now()->addDays(7);
        $this->save();

        return $this->invitation_token;
    }

    public function acceptInvitation(): void
    {
        $this->invitation_accepted = true;
        $this->invitation_accepted_at = now();
        $this->save();
    }

    public function isInvitationExpired(): bool
    {
        return $this->invitation_expires_at && now()->gt($this->invitation_expires_at);
    }

    public function getInvitationUrl(): string
    {
        return url('/invitations/accept/' . $this->invitation_token);
    }
}
