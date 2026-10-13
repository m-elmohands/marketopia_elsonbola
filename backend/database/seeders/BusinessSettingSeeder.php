<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    use SeedTranslations;

    public function run(): void
    {
        $settings = BusinessSetting::firstOrNew(['id' => 1]);
        if (blank($settings->email)) {
            $settings->email = 'zikry0100@gmail.com';
        }
        $this->fillTranslations($settings, [
            'en' => ['brand_name' => 'Golden Spike', 'tagline' => 'Egyptian rice for your table', 'address' => 'Denshal, Damanhour, Beheira, Egypt', 'quality_highlights' => "Egyptian rice.\nFour pack sizes: 1, 5, 10, and 25 kg.\nContact us for current availability."],
            'ar' => ['brand_name' => 'السنبلة الذهبية', 'tagline' => 'أرز مصري لمائدتك', 'address' => 'دنشال، دمنهور، البحيرة، مصر', 'quality_highlights' => "أرز مصري.\nأربعة أحجام: ١ و٥ و١٠ و٢٥ كجم.\nتواصل معنا للاستفسار عن التوفر الحالي."],
        ]);
        // An existing contact list, including deleted records, remains administrator-managed.
        if (! $settings->contactNumbers()->withTrashed()->exists()) {
            $settings->contactNumbers()->create(['phone' => '+201005263014', 'is_primary' => true, 'sort_order' => 0]);
        }
    }
}
