<?php

namespace App\Domain\Content\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DeleteContent
{
    public function execute(Model $record, bool $restore = false): bool
    {
        Gate::authorize($restore ? 'restore' : 'delete', $record);

        return DB::transaction(fn () => $restore ? $record->restore() : $record->delete());
    }
}
