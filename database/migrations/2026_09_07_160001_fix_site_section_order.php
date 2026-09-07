<?php

use App\Support\PageSections;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The seed wrote every sort_order as 0, so the menu came out in whatever
     * order the database happened to return. Put them back into page order —
     * but only where they are still all identical, so a couple who has already
     * reordered the menu keeps their arrangement.
     */
    public function up(): void
    {
        if (DB::table('site_sections')->distinct()->count('sort_order') > 1) {
            return;
        }

        foreach (PageSections::defaults() as $row) {
            DB::table('site_sections')
                ->where('anchor', $row['anchor'])
                ->update(['sort_order' => $row['sort_order']]);
        }
    }

    public function down(): void
    {
        // Nothing to undo — this only repairs ordering.
    }
};
