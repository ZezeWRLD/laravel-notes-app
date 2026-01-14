<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content', // Make sure this is here
        'user_id'
    ];

    public function sentEmails()
    {
        return $this->hasMany(Email::class, 'related_note_id');
    }

    public function shouldSendUpdateNotifications(): bool
    {
        return $this->send_update_notifications;
    }

    public function updateLastNotificationSent($type = 'shared')
    {
        if ($type === 'shared') {
            $this->last_shared_notification_sent_at = now();
        } else {
            $this->last_updated_notification_sent_at = now();
        }

        $this->save();
    }

    /**
     * Get the user that owns the note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
