<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Support\CreatesWebsiteRecord;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use CreatesWebsiteRecord;

    protected static string $resource = ProductResource::class;
}
