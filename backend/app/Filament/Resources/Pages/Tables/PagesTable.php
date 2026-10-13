<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('key')->label(__('Page')),
            TextColumn::make('title')->label(__('Title')),
            IconColumn::make('is_published')->label(__('Published'))->boolean(),
        ])->filters([TrashedFilter::make()])->recordActions([EditAction::make()]);
    }
}
