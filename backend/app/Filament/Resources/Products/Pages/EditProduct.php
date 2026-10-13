<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Support\ContentActions;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [ContentActions::publication(), ...ContentActions::recovery()];
    }
}
