<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\ContentActions;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [ContentActions::publication(), ...ContentActions::recovery(false)];
    }
}
