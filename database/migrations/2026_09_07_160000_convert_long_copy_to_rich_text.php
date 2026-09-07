<?php

use App\Support\RichText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** table => the columns the admin now edits as formatted copy. */
    private const COLUMNS = [
        'settings' => [
            'pull_quote', 'venue_description', 'venue_travel_note', 'schedule_footnote',
            'travel_intro', 'travel_footnote', 'hotels_intro', 'rsvp_body',
        ],
        'hotels' => ['description'],
        'faqs' => ['answer'],
        'notes' => ['body'],
        'highlights' => ['body'],
        'travel_options' => ['body', 'footnote'],
    ];

    /** Existing copy was plain text with markdown links — carry it over as HTML. */
    public function up(): void
    {
        $this->convert(fn (?string $value) => RichText::fromPlainText($value));
    }

    public function down(): void
    {
        $this->convert(fn (?string $value) => RichText::toPlainText($value));
    }

    private function convert(callable $transform): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            DB::table($table)->orderBy('id')->each(function (object $row) use ($table, $columns, $transform) {
                $changes = [];

                foreach ($columns as $column) {
                    if (filled($row->{$column} ?? null)) {
                        $changes[$column] = $transform($row->{$column});
                    }
                }

                if ($changes !== []) {
                    DB::table($table)->where('id', $row->id)->update($changes);
                }
            });
        }
    }
};
