<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_email', 255)->index();
            $table->string('recipient_name', 255)->nullable();

            $table->enum('type', [
                'note_shared',
                'collaborator_invitation',
                'collaborator_added',
                'note_updated',
                'note_commented',
                'note_mentioned',
                'welcome',
                'email_verification',
                'password_reset',
                'system_alert',
                'daily_digest',
                'weekly_summary',
                'marketing',
                'other'
            ])->default('other')->index();

            $table->string('subject', 255);
            $table->text('html_content')->nullable();
            $table->text('text_content')->nullable();
            $table->string('template_slug', 100)->nullable()->index();

            // Tracking
            $table->string('message_id', 255)->nullable()->unique();
            $table->string('provider_id', 255)->nullable()->comment('Email service provider ID');
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);

            // Status
            $table->enum('status', [
                'pending',
                'queued',
                'sending',
                'sent',
                'delivered',
                'failed',
                'bounced',
                'complained'
            ])->default('pending')->index();

            // Error handling
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();

            // Metadata
            $table->json('metadata')->nullable();
            $table->string('campaign_id', 100)->nullable()->index();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();

            // Relationships
            $table->foreignId('related_note_id')->nullable()->constrained('notes')->onDelete('cascade');
            $table->foreignId('related_user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Compound indexes
            $table->index(['user_id', 'type', 'status'], 'idx_user_email_type_status');
            $table->index(['sent_at', 'status'], 'idx_email_sent_status');
            $table->index(['recipient_email', 'created_at'], 'idx_recipient_email_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
