<?php

namespace App\Domain\Shared\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SaveWebsiteRecord
{
    public function execute(Model $record, array $data): Model
    {
        Gate::authorize($record->exists ? 'update' : 'create', $record->exists ? $record : $record::class);
        if (method_exists($record, 'trashed') && $record->trashed()) {
            throw ValidationException::withMessages(['record' => __('Restore this record before editing.')]);
        }
        // Publication is controlled by a dedicated action, never mass-assigned from forms.
        unset($data['is_published']);

        return DB::transaction(function () use ($record, $data) {
            $record->fill($data);
            $record->save();

            return $record;
        });
    }
}
