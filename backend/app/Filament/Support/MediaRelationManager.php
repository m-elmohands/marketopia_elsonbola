<?php

namespace App\Filament\Support;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Media');
    }

    public function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label(__('Name')), TextColumn::make('collection_name')->label(__('Collection')), TextColumn::make('custom_properties.alt.ar')->label(__('Arabic image description'))->wrap(), TextColumn::make('custom_properties.alt.en')->label(__('English image description'))->wrap()])
            ->recordActions([
                EditAction::make()->authorize(fn () => Gate::allows('update', $this->getOwnerRecord()))->schema([
                    TextInput::make('custom_properties.alt.ar')->label(__('Arabic image description'))->required()->maxLength(255),
                    TextInput::make('custom_properties.alt.en')->label(__('English image description'))->required()->maxLength(255),
                    Select::make('custom_properties.locale')->label(__('Artwork language'))->options(['ar' => __('Arabic'), 'en' => __('English')])->placeholder(__('Both languages')),
                ]),
                DeleteAction::make()->authorize(fn () => auth()->user()->isAdministrator() && Gate::allows('update', $this->getOwnerRecord()))->requiresConfirmation()->modalDescription(__('This permanently removes the image file.')),
            ]);
    }
}
