<?php

namespace Database\Factories\Settings;

use App\Domain\Settings\Models\BusinessSetting;
use App\Domain\Settings\Models\ContactNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactNumberFactory extends Factory
{
    protected $model = ContactNumber::class;

    public function definition(): array
    {
        return ['business_setting_id' => BusinessSetting::factory(), 'phone' => '+201005263014', 'is_primary' => true];
    }
}
