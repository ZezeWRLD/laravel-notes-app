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
         Schema::table('notes', function (Blueprint $table) {
            // Add title column after user_id
            $table->string('title')->after('user_id');

            // Add content column after title
            $table->text('content')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });
    }
};
