<?php

namespace App\Filament\Resources\BusinessSettings\Schemas;

use App\Filament\Support\MediaFields;
use App\Filament\Support\TranslationFields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TranslationFields::make(['brand_name' => ['label' => 'Brand name', 'required' => true], 'tagline' => ['label' => 'Tagline'], 'address' => ['label' => 'Address', 'long' => true], 'opening_hours' => ['label' => 'Opening hours', 'long' => true], 'quality_highlights' => ['label' => 'Quality highlights', 'long' => true]]),
            Section::make(__('Contact details'))->schema([
                TextInput::make('email')->label(__('Email'))->email()->maxLength(255),
                TextInput::make('facebook_url')->label(__('Facebook URL'))->url()->maxLength(2048),
                TextInput::make('whatsapp_number')->label(__('WhatsApp number'))->tel()->regex('/^\+[1-9]\d{7,14}$/')->maxLength(20),
                TextInput::make('map_url')->label(__('Map URL'))->url()->maxLength(2048),
                TextInput::make('latitude')->label(__('Latitude'))->numeric()->minValue(-90)->maxValue(90),
                TextInput::make('longitude')->label(__('Longitude'))->numeric()->minValue(-180)->maxValue(180),
            ])->columns(2), MediaFields::make('logo'), ]);
    }
}
