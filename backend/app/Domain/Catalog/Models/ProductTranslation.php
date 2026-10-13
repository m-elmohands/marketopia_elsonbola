<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'short_description', 'description', 'meta_title', 'meta_description', 'locale'];

    public function parent()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
