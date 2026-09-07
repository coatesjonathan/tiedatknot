<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('couple_names')->default('Jenny & Jonny');
            $table->string('first_name')->default('Jenny');
            $table->string('second_name')->default('Jonny');
            $table->string('monogram', 8)->default('JJ');
            $table->date('wedding_date')->nullable();
            $table->string('wedding_time')->nullable();
            $table->string('venue_name')->nullable();
            $table->text('venue_description')->nullable();
            $table->text('venue_address')->nullable();
            $table->text('venue_travel_note')->nullable();
            $table->string('maps_url')->nullable();
            $table->string('location_label')->default('Granada, Spain');
            $table->string('contact_email')->nullable();
            $table->date('rsvp_deadline')->nullable();
            $table->string('block_code')->nullable();
            $table->string('honeymoon_url')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->string('venue_image_path')->nullable();
            $table->text('pull_quote')->nullable();
            $table->text('travel_intro')->nullable();
            $table->text('travel_footnote')->nullable();
            $table->text('hotels_intro')->nullable();
            $table->text('schedule_footnote')->nullable();
            $table->text('rsvp_body')->nullable();
            $table->string('animation_speed')->default('quick');
            $table->boolean('countdown_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
