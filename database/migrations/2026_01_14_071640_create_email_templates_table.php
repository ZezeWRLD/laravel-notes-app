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
         Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('category', 50)->default('system')->index();
            $table->string('subject', 255);
            $table->text('html_content');
            $table->text('text_content')->nullable();
            $table->json('variables')->nullable();
            $table->json('default_values')->nullable();
            $table->string('layout', 50)->default('default')->comment('email.layouts.{layout}');

            // Configuration
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false);
            $table->boolean('can_delete')->default(true);
            $table->integer('priority')->default(1);

            // Tracking
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();

            // Versioning
            $table->integer('version')->default(1);
            $table->foreignId('parent_id')->nullable()->constrained('email_templates')->onDelete('set null');

            // Metadata
            $table->json('metadata')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['slug', 'is_active'], 'idx_template_active');
            $table->index(['category', 'priority'], 'idx_category_priority');
        });

        // Insert default templates
        $this->seedDefaultTemplates();
    }
     private function seedDefaultTemplates(): void
    {
        $now = now();
        $templates = [
            [
                'name' => 'Note Shared Notification',
                'slug' => 'note_shared',
                'category' => 'notification',
                'subject' => '{{sender_name}} shared "{{note_title}}" with you',
                'html_content' => $this->getNoteSharedTemplate(),
                'text_content' => "Hello {{recipient_name}},\n\n{{sender_name}} has shared a note with you:\n\n\"{{note_title}}\"\n\nAccess Level: {{access_level}}\n\nView the note here: {{note_url}}\n\n---\nUnsubscribe: {{unsubscribe_url}}",
                'variables' => json_encode([
                    'sender_name', 'recipient_name', 'note_title',
                    'note_excerpt', 'note_url', 'access_level',
                    'unsubscribe_url', 'app_name'
                ]),
                'default_values' => json_encode(['app_name' => config('app.name')]),
                'is_system' => true,
                'can_delete' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Collaborator Invitation',
                'slug' => 'collaborator_invitation',
                'category' => 'invitation',
                'subject' => 'You\'ve been invited to collaborate on "{{note_title}}"',
                'html_content' => $this->getCollaboratorInvitationTemplate(),
                'text_content' => "Hello {{collaborator_name}},\n\n{{inviter_name}} has invited you to collaborate on a note:\n\n\"{{note_title}}\"\n\nAccess Level: {{access_level}}\n\nInvitation expires: {{expires_at}}\n\nAccept invitation: {{accept_url}}\n\nDecline invitation: {{decline_url}}",
                'variables' => json_encode([
                    'collaborator_name', 'inviter_name', 'note_title',
                    'note_excerpt', 'access_level', 'expires_at',
                    'accept_url', 'decline_url', 'app_name'
                ]),
                'default_values' => json_encode(['app_name' => config('app.name')]),
                'is_system' => true,
                'can_delete' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome',
                'category' => 'system',
                'subject' => 'Welcome to {{app_name}}, {{user_name}}!',
                'html_content' => $this->getWelcomeTemplate(),
                'text_content' => "Welcome to {{app_name}}, {{user_name}}!\n\nWe're excited to have you on board.\n\nGet started: {{dashboard_url}}\n\nVerify your email: {{verification_url}}\n\nNeed help? Visit: {{help_url}}",
                'variables' => json_encode([
                    'user_name', 'app_name', 'dashboard_url',
                    'verification_url', 'help_url', 'contact_email'
                ]),
                'default_values' => json_encode([
                    'app_name' => config('app.name'),
                    'contact_email' => config('mail.from.address')
                ]),
                'is_system' => true,
                'can_delete' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Email Verification',
                'slug' => 'email_verification',
                'category' => 'system',
                'subject' => 'Verify your email address for {{app_name}}',
                'html_content' => $this->getVerificationTemplate(),
                'text_content' => "Please verify your email address.\n\nClick here to verify: {{verification_url}}\n\nThis link will expire in {{expiry_hours}} hours.\n\nIf you didn't create an account, you can ignore this email.",
                'variables' => json_encode([
                    'user_name', 'app_name', 'verification_url',
                    'expiry_hours'
                ]),
                'default_values' => json_encode([
                    'app_name' => config('app.name'),
                    'expiry_hours' => 24
                ]),
                'is_system' => true,
                'can_delete' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Daily Digest',
                'slug' => 'daily_digest',
                'category' => 'digest',
                'subject' => 'Your Daily Notes Digest - {{date}}',
                'html_content' => $this->getDailyDigestTemplate(),
                'text_content' => "Your Daily Notes Digest for {{date}}\n\nNew notes shared with you: {{new_notes_count}}\n\nUpdated notes: {{updated_notes_count}}\n\nNew comments: {{new_comments_count}}\n\nView all activity: {{activity_url}}",
                'variables' => json_encode([
                    'user_name', 'date', 'new_notes_count',
                    'updated_notes_count', 'new_comments_count',
                    'activity_url', 'app_name'
                ]),
                'default_values' => json_encode(['app_name' => config('app.name')]),
                'is_system' => true,
                'can_delete' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->insert($template);
        }
    }

    private function getNoteSharedTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Shared</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px 20px; border-radius: 0 0 8px 8px; }
        .note-card { background: white; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 500; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
        .badge { display: inline-block; padding: 4px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 12px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">📝 Note Shared</h1>
        </div>

        <div class="content">
            <h2 style="margin-top: 0;">Hello {{recipient_name}},</h2>

            <p>{{sender_name}} has shared a note with you:</p>

            <div class="note-card">
                <h3 style="margin: 0 0 10px 0;">{{note_title}}</h3>
                <p style="margin: 0 0 15px 0; color: #6b7280;">{{note_excerpt}}</p>
                <div class="badge">{{access_level}} access</div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{note_url}}" class="button">View Note</a>
            </div>

            <div class="footer">
                <p>You received this email because {{sender_name}} shared a note with you on {{app_name}}.</p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280;">Unsubscribe from these notifications</a>
                    |
                    <a href="{{settings_url}}" style="color: #6b7280;">Notification Settings</a>
                </p>
                <p>© {{year}} {{app_name}}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getCollaboratorInvitationTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborator Invitation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px 20px; border-radius: 0 0 8px 8px; }
        .note-card { background: white; border-left: 4px solid #10b981; padding: 20px; margin: 20px 0; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .button { display: inline-block; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 500; margin: 5px; }
        .accept { background: #10b981; color: white; }
        .decline { background: #ef4444; color: white; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
        .badge { display: inline-block; padding: 4px 8px; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 12px; font-weight: 500; }
        .expiry { background: #fef3c7; color: #92400e; padding: 10px; border-radius: 4px; margin: 15px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">👥 Collaboration Invitation</h1>
        </div>

        <div class="content">
            <h2 style="margin-top: 0;">Hello {{collaborator_name}},</h2>

            <p>{{inviter_name}} has invited you to collaborate on a note:</p>

            <div class="note-card">
                <h3 style="margin: 0 0 10px 0;">{{note_title}}</h3>
                <p style="margin: 0 0 15px 0; color: #6b7280;">{{note_excerpt}}</p>
                <div class="badge">{{access_level}} access</div>
            </div>

            <div class="expiry">
                ⏰ This invitation expires on <strong>{{expires_at}}</strong>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{accept_url}}" class="button accept">Accept Invitation</a>
                <a href="{{decline_url}}" class="button decline">Decline</a>
            </div>

            <div style="background: #eff6ff; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px;">
                    <strong>ℹ️ Note:</strong> If you don't have a {{app_name}} account yet,
                    accepting this invitation will prompt you to create one.
                </p>
            </div>

            <div class="footer">
                <p>You received this invitation from {{inviter_name}} on {{app_name}}.</p>
                <p>© {{year}} {{app_name}}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getWelcomeTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); color: white; padding: 40px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 40px 20px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #8b5cf6; color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 16px; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .feature { background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .feature-icon { font-size: 32px; margin-bottom: 15px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0 0 10px 0;">🎉 Welcome to {{app_name}}, {{user_name}}!</h1>
            <p style="margin: 0; opacity: 0.9;">We're excited to have you on board</p>
        </div>

        <div class="content">
            <p>Thank you for joining {{app_name}}! Here's how to get started:</p>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">📝</div>
                    <h3 style="margin: 10px 0;">Create Notes</h3>
                    <p style="margin: 0; font-size: 14px;">Start by creating your first note</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">👥</div>
                    <h3 style="margin: 10px 0;">Collaborate</h3>
                    <p style="margin: 0; font-size: 14px;">Share notes with your team</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🏷️</div>
                    <h3 style="margin: 10px 0;">Organize</h3>
                    <p style="margin: 0; font-size: 14px;">Use tags and folders</p>
                </div>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{dashboard_url}}" class="button">Go to Dashboard</a>
            </div>

            <div style="background: #eff6ff; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <h3 style="margin-top: 0;">Verify Your Email</h3>
                <p style="margin-bottom: 20px;">Please verify your email address to access all features:</p>
                <div style="text-align: center;">
                    <a href="{{verification_url}}" style="background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        Verify Email Address
                    </a>
                </div>
            </div>

            <div class="footer">
                <p>Need help? Check out our <a href="{{help_url}}" style="color: #8b5cf6;">documentation</a> or <a href="mailto:{{contact_email}}" style="color: #8b5cf6;">contact support</a>.</p>
                <p>© {{year}} {{app_name}}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getVerificationTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px 20px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 16px; }
        .code { background: white; border: 2px dashed #f59e0b; padding: 20px; margin: 20px 0; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
        .warning { background: #fef3c7; color: #92400e; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">🔐 Verify Your Email</h1>
        </div>

        <div class="content">
            <h2 style="margin-top: 0;">Hello {{user_name}},</h2>

            <p>Please verify your email address for your {{app_name}} account by clicking the button below:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{verification_url}}" class="button">Verify Email Address</a>
            </div>

            <p>Or copy and paste this link into your browser:</p>
            <div style="background: #f3f4f6; padding: 15px; border-radius: 4px; margin: 15px 0; word-break: break-all; font-size: 14px;">
                {{verification_url}}
            </div>

            <div class="warning">
                <p style="margin: 0;">⏰ This verification link will expire in <strong>{{expiry_hours}} hours</strong>.</p>
            </div>

            <div class="footer">
                <p>If you didn't create an account with {{app_name}}, you can safely ignore this email.</p>
                <p>© {{year}} {{app_name}}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getDailyDigestTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Digest</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px 20px; border-radius: 0 0 8px 8px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; margin: 15px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-number { font-size: 32px; font-weight: bold; color: #6366f1; margin: 0; }
        .stat-label { color: #6b7280; margin: 5px 0 0 0; }
        .button { display: inline-block; background: #6366f1; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 500; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; text-align: center; }
        .activity-item { display: flex; align-items: center; padding: 15px; background: white; margin: 10px 0; border-radius: 8px; }
        .activity-icon { font-size: 20px; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">📊 Your Daily Digest</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">{{date}}</p>
        </div>

        <div class="content">
            <h2 style="margin-top: 0;">Hello {{user_name}},</h2>
            <p>Here's your activity summary for today:</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 25px 0;">
                <div class="stat-card" style="text-align: center;">
                    <p class="stat-number">{{new_notes_count}}</p>
                    <p class="stat-label">New Notes</p>
                </div>
                <div class="stat-card" style="text-align: center;">
                    <p class="stat-number">{{updated_notes_count}}</p>
                    <p class="stat-label">Updated Notes</p>
                </div>
                <div class="stat-card" style="text-align: center;">
                    <p class="stat-number">{{new_comments_count}}</p>
                    <p class="stat-label">Comments</p>
                </div>
                <div class="stat-card" style="text-align: center;">
                    <p class="stat-number">{{new_shares_count}}</p>
                    <p class="stat-label">New Shares</p>
                </div>
            </div>

            <div style="margin: 30px 0;">
                <h3 style="margin-top: 0;">📈 Recent Activity</h3>
                {% if activities|length > 0 %}
                    {% for activity in activities %}
                    <div class="activity-item">
                        <div class="activity-icon">{{activity.icon}}</div>
                        <div>
                            <p style="margin: 0 0 5px 0; font-weight: 500;">{{activity.title}}</p>
                            <p style="margin: 0; font-size: 14px; color: #6b7280;">{{activity.description}}</p>
                        </div>
                    </div>
                    {% endfor %}
                {% else %}
                    <p style="text-align: center; color: #6b7280; padding: 20px;">No activity today.</p>
                {% endif %}
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{activity_url}}" class="button">View All Activity</a>
            </div>

            <div class="footer">
                <p>You're receiving this email because you subscribed to daily digests on {{app_name}}.</p>
                <p>
                    <a href="{{unsubscribe_url}}" style="color: #6b7280;">Unsubscribe from daily digests</a>
                    |
                    <a href="{{settings_url}}" style="color: #6b7280;">Notification Settings</a>
                </p>
                <p>© {{year}} {{app_name}}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

