<?php

namespace App\Filament\Resources\ContactNumbers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactNumberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('phone')->label(__('Phone'))->tel()->required()->maxLength(20)->regex('/^\+[1-9]\d{7,14}$/')->helperText(__('Use international format, for example +201005263014.'))->unique(ignoreRecord: true),
            Toggle::make('is_primary')->label(__('Primary number'))->default(false),
            TextInput::make('sort_order')->label(__('Display order'))->integer()->minValue(0)->default(0)->required(), ]);
    }
}
