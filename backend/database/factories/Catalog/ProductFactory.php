<?php

namespace Database\Factories\Catalog;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'is_published' => false,
            'ar' => ['name' => 'أرز مصري', 'short_description' => 'أرز مصري', 'description' => 'تفاصيل الأرز'],
            'en' => ['name' => 'Egyptian rice', 'short_description' => 'Egyptian rice', 'description' => 'Rice details'],
        ];
    }
}
