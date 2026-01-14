<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_notifications_enabled')) {
                $table->boolean('email_notifications_enabled')->default(true)->after('email');
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email_notifications_enabled');
            }

            if (!Schema::hasColumn('users', 'unsubscribe_token')) {
                $table->string('unsubscribe_token', 64)->unique()->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'last_email_sent_at')) {
                $table->timestamp('last_email_sent_at')->nullable()->after('unsubscribe_token');
            }

            if (!Schema::hasColumn('users', 'email_verification_token')) {
                $table->string('email_verification_token', 64)->nullable()->after('unsubscribe_token');
            }

            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable()->after('last_email_sent_at');
            }
        });

        // Add indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email_notifications_enabled', 'last_email_sent_at'], 'idx_user_email_settings');
            $table->index('unsubscribe_token', 'idx_unsubscribe_token');
        });

        // Set email_verified_at for existing users
        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => DB::raw('created_at'),
        ]);

        // Generate unsubscribe tokens for existing users
        $users = DB::table('users')->whereNull('unsubscribe_token')->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'unsubscribe_token' => hash('sha256', $user->email . time() . random_bytes(16))
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $columns = [
                'email_notifications_enabled',
                'email_verified_at',
                'unsubscribe_token',
                'last_email_sent_at',
                'email_verification_token',
                'notification_preferences'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            $table->dropIndex('idx_user_email_settings');
            $table->dropIndex('idx_unsubscribe_token');
        });
    }
};
