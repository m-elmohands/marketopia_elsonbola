<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Support\MediaFields;
use App\Filament\Support\TranslationFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([Section::make(__('Product details'))->schema([
            TextInput::make('slug')->label(__('Slug'))->required()->maxLength(255)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true),
            Toggle::make('is_featured')->label(__('Featured')),
            TextInput::make('sort_order')->label(__('Display order'))->integer()->minValue(0)->default(0)->required(),
        ])->columns(2),
            TranslationFields::make([
                'name' => ['label' => 'Name', 'required' => true],
                'short_description' => ['label' => 'Short description', 'long' => true],
                'description' => ['label' => 'Description', 'long' => true],
                'meta_title' => ['label' => 'SEO title'],
                'meta_description' => ['label' => 'SEO description', 'long' => true],
            ]),
            Section::make(__('Images'))->schema([MediaFields::make('cover'), MediaFields::make('gallery', true)])->columns(2), ]);
    }
}
