<?php

namespace App\Models;

use App\Casts\SlugCast;
use Domain\Catalog\Facades\Sorter;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pipeline\Pipeline;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;
use Support\Casts\PriceCast;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $thumbnail
 * @property int $price
 */
class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;

    protected $fillable = [
        'title',
        'thumbnail',
        'slug',
        'price',
        'brand_id',
        'on_home_page',
        'sorting',
        'text'
    ];

    protected $casts = [
        'price' => PriceCast::class
    ];

    protected function thumbnailDir(): string
    {
        return 'products';
    }
    public function scopeFiltered(Builder $query)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through(filters())
            ->via('handle')
            ->thenReturn();
    }

//    public function scopeSorted(Builder $query): void
//    {
//        $query->when(request('sort'), function (Builder $q) {
//            $column = request()->str('sort');
//
//            if ($column->contains(['price', 'title'])) {
//                $direction = $column->contains('-') ? 'DESC' : 'ASC';
//                $q->orderBy((string) $column->remove('-'), $direction);
//
//            }
//
//        });
//
//    }

    public function scopeHomePage(Builder $query): void
    {
        $query->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }

//TODO: разобраться, почему не сработало

//    public function scopeSorted(Builder $query)
//    {
//        Sorter::run($query);
//    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function properties(): BelongsToMany
    {
        return  $this->belongsToMany(Property::class)
            ->withPivot('value');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class);
    }


}
