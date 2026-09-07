<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->unsignedTinyInteger('seats')->default(1);
            $table->string('rsvp_status')->default('pending');
            $table->text('rsvp_note')->nullable();
            $table->text('dietary')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->unsignedInteger('unlock_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
