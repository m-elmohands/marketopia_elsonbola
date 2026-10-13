<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label(__('Name'))->required()->maxLength(255),
            TextInput::make('email')->label(__('Email'))->email()->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('password')->label(__('Password'))->password()->revealable()->minLength(12)->required(fn (string $operation) => $operation === 'create')->dehydrated(fn ($state) => filled($state)),
            Select::make('role')->label(__('Role'))->options(['admin' => __('Administrator'), 'editor' => __('Editor')])->required()->default('editor'),
            Toggle::make('is_active')->label(__('Active'))->default(true), ]);
    }
}
