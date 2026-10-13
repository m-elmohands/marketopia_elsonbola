<?php

namespace App\Filament\Support;

use App\Domain\Shared\Actions\SaveWebsiteRecord;
use Illuminate\Database\Eloquent\Model;

trait CreatesWebsiteRecord
{
    protected function handleRecordCreation(array $data): Model
    {
        $model = static::getResource()::getModel();

        return app(SaveWebsiteRecord::class)->execute(new $model, $data);
    }
}
