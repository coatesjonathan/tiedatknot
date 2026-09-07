<?php

use App\Support\PageSections;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per section of the invitation: whether it is shown at all,
     * whether it appears in the menu, and what the menu calls it.
     */
    public function up(): void
    {
        Schema::create('site_sections', function (Blueprint $table) {
            $table->id();
            $table->string('anchor')->unique();
            $table->string('label');
            $table->boolean('is_published')->default(true);
            $table->boolean('in_menu')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seeded so everything works straight away and every row is editable.
        DB::table('site_sections')->insert(array_map(
            fn (array $row) => $row + ['created_at' => now(), 'updated_at' => now()],
            PageSections::defaults()
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('site_sections');
    }
};
