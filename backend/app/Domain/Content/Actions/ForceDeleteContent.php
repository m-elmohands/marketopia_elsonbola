<?php

namespace App\Domain\Content\Actions;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ForceDeleteContent
{
    public function execute(Model $record): void
    {
        Gate::authorize('forceDelete', $record);
        DB::transaction(function () use ($record): void {
            if ($record instanceof Product) {
                foreach ($record->variants()->withTrashed()->get() as $variant) {
                    $variant->forceDelete();
                }
            }
            $record->forceDelete();
        });
    }
}
