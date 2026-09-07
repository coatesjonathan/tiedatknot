<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who is actually coming. A guest may hold several seats, so the reply names
     * each person in the party and carries their own dietary needs — replacing
     * the single free-text `dietary` note we kept on the guest.
     */
    public function up(): void
    {
        Schema::create('rsvp_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('dietary')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Carry any dietary note already recorded over to the lead attendee.
        DB::table('guests')
            ->whereNotNull('dietary')
            ->where('dietary', '!=', '')
            ->orderBy('id')
            ->each(function (object $guest) {
                DB::table('rsvp_attendees')->insert([
                    'guest_id' => $guest->id,
                    'name' => $guest->name,
                    'dietary' => $guest->dietary,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('dietary');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->text('dietary')->nullable()->after('rsvp_note');
        });

        Schema::dropIfExists('rsvp_attendees');
    }
};
