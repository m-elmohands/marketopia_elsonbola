<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Page;
use App\Domain\Settings\Models\BusinessSetting;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class WebsiteMediaSeeder extends Seeder
{
    public function run(): void
    {
        $settings = BusinessSetting::find(1);
        if ($settings) {
            $this->attach($settings, 'logo', 'logo.jpg', ['en' => 'Golden Spike logo', 'ar' => 'شعار السنبلة الذهبية']);
        }
        $product = Product::where('slug', 'egyptian-rice')->first();
        if ($product) {
            $this->attach($product, 'cover', 'rice-still-life.png', ['en' => 'Illustrative Egyptian rice arrangement', 'ar' => 'صورة توضيحية للأرز المصري']);
            foreach ($product->variants as $variant) {
                $weight = (int) $variant->weight_kg;
                if (in_array($weight, [1, 5, 10, 25], true)) {
                    $this->attach($variant, 'packaging', "rice-{$weight}kg.jpg", ['en' => "Golden Spike {$weight} kg promotional artwork", 'ar' => "تصميم ترويجي لعبوة السنبلة الذهبية {$weight} كجم"], 'ar');
                }
            }
        }
        foreach (Page::whereIn('key', ['home', 'about'])->get() as $page) {
            $this->attach($page, 'hero', 'rice-still-life.png', ['en' => 'Illustrative rice still life', 'ar' => 'صورة توضيحية للأرز']);
        }
    }

    private function attach(HasMedia $record, string $collection, string $file, array $alt, ?string $locale = null): void
    {
        if ($record->getMedia($collection)->isNotEmpty()) {
            return;
        }
        $record->addMedia(__DIR__.'/assets/'.$file)->preservingOriginal()
            ->withCustomProperties(['alt' => $alt, 'locale' => $locale])
            ->toMediaCollection($collection);
    }
}
