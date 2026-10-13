<?php

namespace App\Domain\Settings\Actions;

use App\Domain\Settings\Models\BusinessSetting;

class GetBusinessSettings
{
    public function execute(string $locale): BusinessSetting
    {
        return BusinessSetting::whereKey(1)->whereHas('translations', fn ($q) => $q->where('locale', $locale))->with(['translations', 'media', 'contactNumbers'])->firstOrFail();
    }
}
