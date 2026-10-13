<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Filament\Support\MediaFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class ProductVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([Section::make(__('Pack details'))->schema([
            Select::make('product_id')->label(__('Product'))->relationship('product', 'slug')->searchable()->preload()->required()->disabled(fn ($record) => $record?->exists)->dehydrated(),
            TextInput::make('weight_kg')->label(__('Weight in kg'))->numeric()->step(0.001)->minValue(0.001)->maxValue(99999.999)->required()->rules([fn ($record, Get $get) => Rule::unique('product_variants', 'weight_kg')->where('product_id', $get('product_id'))->ignore($record?->id)]),
            Toggle::make('is_active')->label(__('Active'))->default(true),
            TextInput::make('sort_order')->label(__('Display order'))->integer()->minValue(0)->default(0)->required(),
        ])->columns(2), MediaFields::make('packaging'), ]);
    }
}
