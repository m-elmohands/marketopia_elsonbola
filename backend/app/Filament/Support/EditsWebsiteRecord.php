<?php

namespace App\Filament\Support;

use App\Domain\Shared\Actions\SaveWebsiteRecord;
use Illuminate\Database\Eloquent\Model;

trait EditsWebsiteRecord
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (method_exists($this->record, 'translate')) {
            foreach (['ar', 'en'] as $locale) {
                $data[$locale] = $this->record->translate($locale, false)?->attributesToArray() ?? [];
            }
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(SaveWebsiteRecord::class)->execute($record, $data);
    }
}
