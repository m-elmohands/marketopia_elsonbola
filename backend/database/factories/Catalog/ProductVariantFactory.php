<?php

namespace Database\Factories\Catalog;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'weight_kg' => fake()->randomElement([1, 5, 10, 25]), 'is_active' => true];
    }
}
