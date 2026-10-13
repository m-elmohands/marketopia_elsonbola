<?php

namespace App\Filament\Resources\ContactNumbers\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ContactNumbersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('phone')->label(__('Phone'))->searchable(),
            IconColumn::make('is_primary')->label(__('Primary number'))->boolean(),
            TextColumn::make('sort_order')->label(__('Display order'))->sortable(),
        ])->filters([TrashedFilter::make()])->recordActions([EditAction::make()])->defaultSort('sort_order');
    }
}
