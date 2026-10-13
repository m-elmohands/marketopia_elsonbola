<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\ContentActions;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return ContentActions::recovery(false);
    }
}
