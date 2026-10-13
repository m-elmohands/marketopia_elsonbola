<?php

namespace App\Filament\Resources\ProductVariants\Pages;

use App\Filament\Resources\ProductVariants\ProductVariantResource;
use App\Filament\Support\ContentActions;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;

class EditProductVariant extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = ProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return ContentActions::recovery();
    }
}
