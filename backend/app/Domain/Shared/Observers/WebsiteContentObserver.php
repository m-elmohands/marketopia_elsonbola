<?php

namespace App\Domain\Shared\Observers;

use App\Domain\Audit\Services\RecordAudit;
use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Page;
use App\Domain\Content\Services\PublicationValidator;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Database\Eloquent\Model;

class WebsiteContentObserver
{
    public function saving(Model $record): void
    {
        if (($record instanceof Product || $record instanceof Page) && $record->is_published) {
            app(PublicationValidator::class)->validate($record);
        }
    }

    public function created(Model $record): void
    {
        $this->changed($record, 'created');
    }

    public function updated(Model $record): void
    {
        $this->changed($record, 'updated');
    }

    public function deleted(Model $record): void
    {
        $this->changed($record, method_exists($record, 'isForceDeleting') && $record->isForceDeleting() ? 'force_deleted' : 'deleted');
    }

    public function restored(Model $record): void
    {
        $this->changed($record, 'restored');
    }

    private function changed(Model $record, string $action): void
    {
        app(RecordAudit::class)->execute($record, $action);
        app(ContentCache::class)->invalidate();
    }
}
