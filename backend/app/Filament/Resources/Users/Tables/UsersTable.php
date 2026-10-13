<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name'))->searchable(),
            TextColumn::make('email')->label(__('Email'))->searchable(),
            TextColumn::make('role')->label(__('Role'))->formatStateUsing(fn ($state) => __($state === 'admin' ? 'Administrator' : 'Editor')),
            IconColumn::make('is_active')->label(__('Active'))->boolean(),
        ])->filters([TrashedFilter::make()])->recordActions([EditAction::make()]);
    }
}
