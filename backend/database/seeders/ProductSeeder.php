<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use SeedTranslations;

    public function run(): void
    {
        $product = Product::withTrashed()->firstOrNew(['slug' => 'egyptian-rice']);
        if ($product->trashed()) {
            return;
        }
        $isNew = ! $product->exists;
        if ($isNew) {
            $product->fill(['is_published' => false, 'is_featured' => true, 'sort_order' => 0]);
        }
        $this->fillTranslations($product, [
            'en' => [
                'name' => 'Egyptian rice',
                'short_description' => 'Egyptian rice from Golden Spike, available in four practical pack sizes.',
                'description' => 'Discover Egyptian rice from Golden Spike in Denshal, Damanhour, Beheira. Choose a pack for everyday cooking, family meals, or larger supply needs.

Available in 1, 5, 10, and 25 kg packs. Contact our team to discuss current availability and supply enquiries.',
                'meta_title' => 'Egyptian rice | Golden Spike',
                'meta_description' => 'Explore Golden Spike Egyptian rice in 1, 5, 10, and 25 kg packs. Contact our team in Denshal, Damanhour for availability.',
            ],
            'ar' => [
                'name' => 'أرز مصري',
                'short_description' => 'أرز مصري من السنبلة الذهبية، متوفر بأربعة أحجام تناسب احتياجاتك.',
                'description' => 'اكتشف الأرز المصري من السنبلة الذهبية في دنشال، دمنهور، البحيرة. اختر العبوة المناسبة للطهي اليومي والوجبات العائلية واحتياجات التوريد الأكبر.

متوفر بعبوات ١ و٥ و١٠ و٢٥ كجم. تواصل مع فريقنا للاستفسار عن التوفر الحالي وطلبات التوريد.',
                'meta_title' => 'أرز مصري | السنبلة الذهبية',
                'meta_description' => 'تعرّف على الأرز المصري من السنبلة الذهبية بعبوات ١ و٥ و١٠ و٢٥ كجم. تواصل معنا في دنشال، دمنهور للاستفسار عن التوفر.',
            ],
        ]);
        $this->call(PackSizeSeeder::class);
        if ($isNew) {
            $product->update(['is_published' => true]);
        }
    }
}
