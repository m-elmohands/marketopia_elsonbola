<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\MediaFields;
use App\Filament\Support\TranslationFields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->label(__('Page'))->disabled()->dehydrated(false),
            TranslationFields::make([
                'title' => ['label' => 'Title', 'required' => true],
                'intro' => ['label' => 'Introduction', 'long' => true],
                'body' => ['label' => 'Body', 'long' => true],
                'meta_title' => ['label' => 'SEO title'],
                'meta_description' => ['label' => 'SEO description', 'long' => true],
            ]),
            Section::make(__('Images'))->schema([MediaFields::make('hero'), MediaFields::make('gallery', true)])->columns(2), ]);
    }
}
