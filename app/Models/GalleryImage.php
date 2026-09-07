<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_wide' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
