<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->slug = $model->slug
                ?? str(self::slugFrom($model))
                    ->append(time())
                    ->slug();
        });
    }

    public static function slugFrom(Model $model): string
    {
        return $model->title;
    }
}
