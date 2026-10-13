<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name'))->searchable(query: fn ($query, $search) => $query->whereHas('translations', fn ($q) => $q->where('name', 'like', "%$search%"))),
            TextColumn::make('slug')->label(__('Slug')),
            IconColumn::make('is_published')->label(__('Published'))->boolean(),
            IconColumn::make('is_featured')->label(__('Featured'))->boolean(),
            TextColumn::make('sort_order')->label(__('Display order'))->sortable(),
        ])->filters([TrashedFilter::make(), TernaryFilter::make('is_published')->label(__('Published')), TernaryFilter::make('is_featured')->label(__('Featured'))])->recordActions([EditAction::make()])->defaultSort('sort_order');
    }
}
