<?php

namespace App\Filament\Resources\ContactNumbers;

use App\Domain\Settings\Models\ContactNumber;
use App\Filament\Resources\ContactNumbers\Pages\CreateContactNumber;
use App\Filament\Resources\ContactNumbers\Pages\EditContactNumber;
use App\Filament\Resources\ContactNumbers\Pages\ListContactNumbers;
use App\Filament\Resources\ContactNumbers\Schemas\ContactNumberForm;
use App\Filament\Resources\ContactNumbers\Tables\ContactNumbersTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactNumberResource extends Resource
{
    protected static ?string $model = ContactNumber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return __('Contact number');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contact numbers');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return ContactNumberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactNumbersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactNumbers::route('/'),
            'create' => CreateContactNumber::route('/create'),
            'edit' => EditContactNumber::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
