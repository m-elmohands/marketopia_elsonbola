<?php

namespace App\Filament\Resources\BusinessSettings\Pages;

use App\Filament\Resources\BusinessSettings\BusinessSettingResource;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;

class EditBusinessSetting extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = BusinessSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
