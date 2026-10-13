<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;

trait SeedTranslations
{
    /** Fill missing fields without replacing administrator-authored content. */
    private function fillTranslations(Model $record, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            $translation = $record->translateOrNew($locale);
            foreach ($fields as $field => $value) {
                if (blank($translation->{$field})) {
                    $translation->{$field} = $value;
                }
            }
        }
        $record->save();
    }
}
