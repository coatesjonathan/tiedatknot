<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteSection extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'in_menu' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** Sections the guest is allowed to see. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    /** anchor => shown, for the page to switch each section on or off. */
    public static function visibility(): array
    {
        return static::query()->pluck('is_published', 'anchor')
            ->map(fn ($shown) => (bool) $shown)
            ->all();
    }
}
