<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'rsvp_deadline' => 'date',
            'countdown_enabled' => 'boolean',
            'gift_payment_methods' => 'array',
            'meal_options' => 'array',
        ];
    }

    /**
     * The main-course labels guests choose between, in order. The first is
     * what's being served and is picked for everyone unless they change it.
     *
     * @return list<string>
     */
    public function mealLabels(): array
    {
        return collect($this->meal_options ?? [])
            ->filter(fn (?string $label) => filled($label))
            ->values()
            ->all();
    }

    /** The singleton row. Created on first access so the panel always has something to edit. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
