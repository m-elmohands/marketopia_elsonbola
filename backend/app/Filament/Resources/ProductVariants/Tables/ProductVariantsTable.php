<?php

namespace App\Filament\Resources\ProductVariants\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('product.name')->label(__('Product')),
            TextColumn::make('weight_kg')->label(__('Weight in kg'))->sortable(),
            IconColumn::make('is_active')->label(__('Active'))->boolean(),
        ])->filters([TrashedFilter::make()])->recordActions([EditAction::make()]);
    }
}
