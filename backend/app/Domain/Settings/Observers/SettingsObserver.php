<?php

namespace App\Domain\Settings\Observers;

use App\Domain\Settings\Models\BusinessSetting;
use App\Domain\Settings\Models\ContactNumber;
use Illuminate\Validation\ValidationException;

class SettingsObserver
{
    public function saving(BusinessSetting|ContactNumber $record): void
    {
        if ($record instanceof BusinessSetting && $record->getKey() !== 1) {
            throw ValidationException::withMessages(['id' => __('Only one business settings record is allowed.')]);
        }
    }

    public function deleting(BusinessSetting|ContactNumber $record): void
    {
        if ($record instanceof BusinessSetting || $record->is_primary) {
            throw ValidationException::withMessages(['record' => __('Choose another primary number first.')]);
        }
    }
}
