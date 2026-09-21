<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('site_sections')->updateOrInsert(
            ['anchor' => 'gifts'],
            [
                'label' => 'Gifts',
                'is_published' => true,
                'in_menu' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('site_sections')->where('anchor', 'good-to-know')->update(['sort_order' => 8]);
        DB::table('site_sections')->where('anchor', 'questions')->update(['sort_order' => 9]);
        DB::table('site_sections')->where('anchor', 'rsvp')->update(['sort_order' => 10]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_sections')->where('anchor', 'gifts')->delete();
    }
};
