<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to notes table
        Schema::table('notes', function (Blueprint $table) {
            if (!Schema::hasColumn('notes', 'last_shared_notification_sent_at')) {
                $table->timestamp('last_shared_notification_sent_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('notes', 'last_updated_notification_sent_at')) {
                $table->timestamp('last_updated_notification_sent_at')->nullable()->after('last_shared_notification_sent_at');
            }

            if (!Schema::hasColumn('notes', 'send_update_notifications')) {
                $table->boolean('send_update_notifications')->default(true)->after('last_updated_notification_sent_at');
            }

            if (!Schema::hasColumn('notes', 'notification_settings')) {
                $table->json('notification_settings')->nullable()->after('send_update_notifications');
            }

            // Add indexes
            $table->index('last_shared_notification_sent_at', 'idx_note_shared_notif');
            $table->index('last_updated_notification_sent_at', 'idx_note_updated_notif');
            $table->index('send_update_notifications', 'idx_note_send_updates');
        });

        // Add columns to collaborators table
        Schema::table('collaborators', function (Blueprint $table) {
            if (!Schema::hasColumn('collaborators', 'invitation_sent_at')) {
                $table->timestamp('invitation_sent_at')->nullable()->after('access_level');
            }

            if (!Schema::hasColumn('collaborators', 'invitation_token')) {
                $table->string('invitation_token', 64)->unique()->nullable()->after('invitation_sent_at');
            }

            if (!Schema::hasColumn('collaborators', 'invitation_expires_at')) {
                $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');
            }

            if (!Schema::hasColumn('collaborators', 'invitation_accepted')) {
                $table->boolean('invitation_accepted')->default(false)->after('invitation_expires_at');
            }

            if (!Schema::hasColumn('collaborators', 'invitation_accepted_at')) {
                $table->timestamp('invitation_accepted_at')->nullable()->after('invitation_accepted');
            }

            if (!Schema::hasColumn('collaborators', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('invitation_accepted_at');
            }

            // Add indexes
            $table->index(['invitation_token', 'invitation_accepted'], 'idx_collab_invitation');
            $table->index('invitation_expires_at', 'idx_collab_expiry');
            $table->index('invitation_accepted', 'idx_collab_accepted');
        });

        // Generate invitation tokens for existing collaborators without one
        DB::table('collaborators')
            ->whereNull('invitation_token')
            ->where('invitation_accepted', false)
            ->update([
                'invitation_token' => DB::raw('UUID()'),
                'invitation_expires_at' => DB::raw('DATE_ADD(NOW(), INTERVAL 7 DAY)')
            ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Remove columns from notes table
        Schema::table('notes', function (Blueprint $table) {
            $columns = [
                'last_shared_notification_sent_at',
                'last_updated_notification_sent_at',
                'send_update_notifications',
                'notification_settings'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('notes', $column)) {
                    $table->dropColumn($column);
                }
            }

            $indexes = [
                'idx_note_shared_notif',
                'idx_note_updated_notif',
                'idx_note_send_updates'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('notes', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        // Remove columns from collaborators table
        Schema::table('collaborators', function (Blueprint $table) {
            $columns = [
                'invitation_sent_at',
                'invitation_token',
                'invitation_expires_at',
                'invitation_accepted',
                'invitation_accepted_at',
                'notification_preferences'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('collaborators', $column)) {
                    $table->dropColumn($column);
                }
            }

            $indexes = [
                'idx_collab_invitation',
                'idx_collab_expiry',
                'idx_collab_accepted'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('collaborators', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
