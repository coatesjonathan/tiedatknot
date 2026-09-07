<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // How many of the saved seats they are actually taking. Null until they reply.
            $table->unsignedTinyInteger('attending_count')->nullable()->after('seats');
            $table->timestamp('rsvp_submitted_at')->nullable()->after('dietary');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['attending_count', 'rsvp_submitted_at']);
        });
    }
};
