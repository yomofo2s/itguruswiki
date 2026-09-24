<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{
    /** Create a slug from $title that is unique in the model's table. */
    public static function unique(Model $model, string $title, string $column = 'slug'): string
    {
        $base = Str::slug($title) ?: Str::lower(Str::random(8));
        $base = Str::limit($base, 180, '');
        $slug = $base;
        $i = 2;

        while ($model->newQuery()
            ->where($column, $slug)
            ->when($model->exists, fn ($q) => $q->whereKeyNot($model->getKey()))
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
