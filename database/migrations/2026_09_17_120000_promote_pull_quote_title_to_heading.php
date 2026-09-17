<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The pull quote's title used to be written as a bold first paragraph, which
 * the section styled up into a serif display line. Now that the editor offers
 * real headings, that opening line becomes an `h1` — the big script flourish.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->rewrite(
            '/^\s*<p[^>]*>\s*<strong>(.*?)<\/strong>\s*<\/p>/is',
            '<h1>$1</h1>'
        );
    }

    public function down(): void
    {
        $this->rewrite(
            '/^\s*<h1[^>]*>(.*?)<\/h1>/is',
            '<p><strong>$1</strong></p>'
        );
    }

    private function rewrite(string $pattern, string $replacement): void
    {
        DB::table('settings')->orderBy('id')->each(function (object $row) use ($pattern, $replacement) {
            if (blank($row->pull_quote)) {
                return;
            }

            $rewritten = preg_replace($pattern, $replacement, $row->pull_quote, 1);

            if ($rewritten !== null && $rewritten !== $row->pull_quote) {
                DB::table('settings')->where('id', $row->id)->update(['pull_quote' => $rewritten]);
            }
        });
    }
};
