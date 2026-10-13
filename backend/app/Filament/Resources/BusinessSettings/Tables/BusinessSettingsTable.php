<?php

namespace App\Filament\Resources\BusinessSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BusinessSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('brand_name')->label(__('Brand name')),
            TextColumn::make('email')->label(__('Email')),
        ])->filters([])->recordActions([EditAction::make()]);
    }
}
