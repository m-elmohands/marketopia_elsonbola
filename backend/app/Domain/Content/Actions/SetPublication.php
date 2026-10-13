<?php

namespace App\Domain\Content\Actions;

use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Page;
use App\Domain\Content\Services\PublicationValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SetPublication
{
    public function execute(Product|Page $record, bool $publish): void
    {
        Gate::authorize('update', $record);
        DB::transaction(function () use ($record, $publish): void {
            $record->newQuery()->whereKey($record->getKey())->lockForUpdate()->firstOrFail();
            $record->refresh();
            if ($record->trashed()) {
                throw ValidationException::withMessages(['record' => __('Restore this record before editing.')]);
            }
            if ($publish) {
                app(PublicationValidator::class)->validate($record);
            }
            $record->update(['is_published' => $publish]);
        });
    }
}
