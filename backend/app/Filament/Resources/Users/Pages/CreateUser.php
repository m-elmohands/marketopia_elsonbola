<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\CreatesWebsiteRecord;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use CreatesWebsiteRecord;

    protected static string $resource = UserResource::class;
}
