<?php

namespace Database\Factories\Settings;

use App\Domain\Settings\Models\BusinessSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessSettingFactory extends Factory
{
    protected $model = BusinessSetting::class;

    public function definition(): array
    {
        return ['id' => 1, 'ar' => ['brand_name' => 'السنبلة الذهبية'], 'en' => ['brand_name' => 'Golden Spike']];
    }
}
