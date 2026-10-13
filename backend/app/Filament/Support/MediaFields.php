<?php

namespace App\Filament\Support;

use App\Domain\Shared\Services\MediaMetadata;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;

class MediaFields
{
    public static function make(string $collection, bool $multiple = false): SpatieMediaLibraryFileUpload
    {
        $field = SpatieMediaLibraryFileUpload::make($collection)
            ->label(__(ucfirst($collection)))
            ->collection($collection)
            ->disk('public')
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(5120)
            ->deletable(false)
            ->customProperties(fn (?Model $record, Get $get) => app(MediaMetadata::class)
                ->alternatives($record, fn ($key) => $get($key)))
            ->helperText(__('Manage image descriptions and removal in the media table below.'));

        return $multiple ? $field->multiple()->reorderable()->maxFiles(12) : $field;
    }
}
