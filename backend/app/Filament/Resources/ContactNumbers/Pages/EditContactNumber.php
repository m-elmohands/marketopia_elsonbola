<?php

namespace App\Filament\Resources\ContactNumbers\Pages;

use App\Domain\Settings\Actions\SaveContactNumber;
use App\Filament\Resources\ContactNumbers\ContactNumberResource;
use App\Filament\Support\ContentActions;
use App\Filament\Support\EditsWebsiteRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContactNumber extends EditRecord
{
    use EditsWebsiteRecord;

    protected static string $resource = ContactNumberResource::class;

    protected function getHeaderActions(): array
    {
        return ContentActions::recovery();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(SaveContactNumber::class)->execute($data, $record);
    }
}
