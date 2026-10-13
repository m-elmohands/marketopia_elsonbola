<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Shared\Concerns\HasWebsiteMedia;
use Database\Factories\Catalog\ProductVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, HasWebsiteMedia, SoftDeletes;

    protected $fillable = ['product_id', 'weight_kg', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['weight_kg' => 'decimal:3', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory()
    {
        return ProductVariantFactory::new();
    }
}
