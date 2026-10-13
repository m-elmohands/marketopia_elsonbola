<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Shared\Concerns\HasWebsiteMedia;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Database\Factories\Catalog\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia, TranslatableContract
{
    use HasFactory, HasWebsiteMedia, SoftDeletes, Translatable;

    protected $fillable = ['slug', 'is_published', 'is_featured', 'sort_order'];

    public $translatedAttributes = ['name', 'short_description', 'description', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_featured' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('weight_kg');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereHas('variants', fn (Builder $q) => $q->where('is_active', true));
    }
}
