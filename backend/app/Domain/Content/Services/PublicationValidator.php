<?php

namespace App\Domain\Content\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Page;
use Illuminate\Validation\ValidationException;

class PublicationValidator
{
    public function validate(Product|Page $record): void
    {
        $fields = $record instanceof Product ? ['name', 'short_description', 'description'] : ($record->key === 'about' ? ['title', 'intro', 'body'] : ['title', 'intro']);
        $errors = [];
        foreach (config('website.locales') as $locale) {
            $translation = $record->translate($locale, false);
            foreach ($fields as $field) {
                if (! $translation || blank($translation->{$field})) {
                    $errors["$locale.$field"] = __('Complete both language versions before publishing.');
                }
            }
        }
        if ($record instanceof Product && (! $record->exists || ! $record->variants()->where('is_active', true)->exists())) {
            $errors['is_published'] = __('Add an active pack size before publishing.');
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
