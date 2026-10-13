<?php

namespace App\Filament\Resources\ContactNumbers\Pages;

use App\Domain\Settings\Actions\SaveContactNumber;
use App\Filament\Resources\ContactNumbers\ContactNumberResource;
use App\Filament\Support\CreatesWebsiteRecord;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateContactNumber extends CreateRecord
{
    use CreatesWebsiteRecord;

    protected static string $resource = ContactNumberResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SaveContactNumber::class)->execute($data);
    }
}
