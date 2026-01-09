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
        // First, check if the table exists
        if (Schema::hasTable('notes')) {
            // Check if user_id column already exists
            if (!Schema::hasColumn('notes', 'user_id')) {
                Schema::table('notes', function (Blueprint $table) {
                    // Add user_id column as foreign key
                    $table->foreignId('user_id')
                          ->nullable() // Make nullable first to add to existing table
                          ->constrained('users')
                          ->onDelete('cascade')
                          ->after('id');
                });

                // If you have existing notes, you might want to assign them to a user
                // This sets all existing notes to the first user (or admin)
                if (\App\Models\Note::count() > 0 && \App\Models\User::count() > 0) {
                    $firstUserId = \App\Models\User::first()->id;
                    \App\Models\Note::whereNull('user_id')->update(['user_id' => $firstUserId]);
                }

                // Now make it not nullable
                Schema::table('notes', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable(false)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notes') && Schema::hasColumn('notes', 'user_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
