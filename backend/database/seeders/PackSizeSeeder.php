<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Seeder;

class PackSizeSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::where('slug', 'egyptian-rice')->first();
        if (! $product) {
            return;
        }
        foreach ([1, 5, 10, 25] as $order => $weight) {
            $product->variants()->withTrashed()->firstOrCreate(
                ['weight_kg' => $weight],
                ['is_active' => true, 'sort_order' => $order],
            );
        }
    }
}
