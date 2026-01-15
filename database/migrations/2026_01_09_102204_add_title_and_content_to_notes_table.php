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
        // Only add the columns if they don't already exist (prevents duplicate column errors in tests)
        if (!Schema::hasColumn('notes', 'title')) {
            Schema::table('notes', function (Blueprint $table) {
                // Add title column after user_id
                $table->string('title')->after('user_id');
            });
        }

        if (!Schema::hasColumn('notes', 'content')) {
            Schema::table('notes', function (Blueprint $table) {
                // Add content column after title
                $table->text('content')->nullable()->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('notes', 'title') || Schema::hasColumn('notes', 'content')) {
            Schema::table('notes', function (Blueprint $table) {
                if (Schema::hasColumn('notes', 'title')) {
                    $table->dropColumn('title');
                }
                if (Schema::hasColumn('notes', 'content')) {
                    $table->dropColumn('content');
                }
            });
        }
    }
};
