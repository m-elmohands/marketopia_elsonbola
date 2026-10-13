<?php

namespace App\Domain\Shared\Observers;

use App\Domain\Audit\Services\RecordAudit;
use App\Domain\Catalog\Models\ProductTranslation;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TranslationObserver
{
    public function saving(Model $record): void
    {
        if (! in_array($record->locale, config('website.locales'), true)) {
            throw ValidationException::withMessages(['locale' => __('Unsupported language.')]);
        }
        $parent = $record->parent()->first();
        if ($parent?->is_published) {
            $fields = $record instanceof ProductTranslation ? [
                'name',
                'short_description',
                'description',
            ] : ($parent->key === 'about' ? ['title', 'intro', 'body'] : ['title', 'intro']);
            foreach ($fields as $field) {
                if (blank($record->{$field})) {
                    throw ValidationException::withMessages(["{$record->locale}.$field" => __('Complete both language versions before publishing.')]);
                }
            }
        }
    }

    public function deleting(Model $record): void
    {
        if ($record->parent()->first()?->is_published) {
            throw ValidationException::withMessages(['translation' => __('Unpublish this content before removing a translation.')]);
        }
    }

    public function saved(Model $record): void
    {
        app(RecordAudit::class)->execute($record, 'translation_saved');
        app(ContentCache::class)->invalidate();
    }

    public function deleted(Model $record): void
    {
        app(ContentCache::class)->invalidate();
    }
}
