<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The main courses on offer, set by the couple, and the one each person in
     * a party picked. The attendee keeps the option's label rather than a key,
     * so renaming an option later never rewrites a reply already given.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('meal_options')->nullable();
        });

        DB::table('settings')->whereNull('meal_options')->update([
            'meal_options' => json_encode(['Beef', 'Chicken', 'Vegetarian']),
        ]);

        Schema::table('rsvp_attendees', function (Blueprint $table) {
            $table->string('meal')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsvp_attendees', function (Blueprint $table) {
            $table->dropColumn('meal');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('meal_options');
        });
    }
};
