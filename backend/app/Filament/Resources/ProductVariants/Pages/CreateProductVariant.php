<?php

namespace App\Filament\Resources\ProductVariants\Pages;

use App\Filament\Resources\ProductVariants\ProductVariantResource;
use App\Filament\Support\CreatesWebsiteRecord;
use Filament\Resources\Pages\CreateRecord;

class CreateProductVariant extends CreateRecord
{
    use CreatesWebsiteRecord;

    protected static string $resource = ProductVariantResource::class;
}
