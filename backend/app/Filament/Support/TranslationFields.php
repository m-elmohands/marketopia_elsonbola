<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class TranslationFields
{
    public static function make(array $fields): Tabs
    {
        $tabs = [];
        foreach (['ar' => 'Arabic', 'en' => 'English'] as $locale => $label) {
            $components = [];
            foreach ($fields as $name => $options) {
                $field = ($options['long'] ?? false) ? Textarea::make("$locale.$name")->rows(5)->maxLength(20000) : TextInput::make("$locale.$name")->maxLength(255);
                $components[] = $field->label(__($options['label']))->required($options['required'] ?? false)->extraInputAttributes(['dir' => $locale === 'ar' ? 'rtl' : 'ltr']);
            }
            $tabs[] = Tab::make(__($label))->schema($components);
        }

        return Tabs::make('translations')->tabs($tabs)->columnSpanFull();
    }
}
