<?php

namespace App\Filament\Resources\ContactNumbers\Pages;

use App\Filament\Resources\ContactNumbers\ContactNumberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactNumbers extends ListRecords
{
    protected static string $resource = ContactNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
