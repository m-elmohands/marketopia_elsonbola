<?php

namespace App\Filament\Resources\BusinessSettings;

use App\Domain\Settings\Models\BusinessSetting;
use App\Filament\Resources\BusinessSettings\Pages\EditBusinessSetting;
use App\Filament\Resources\BusinessSettings\Pages\ListBusinessSettings;
use App\Filament\Resources\BusinessSettings\Schemas\BusinessSettingForm;
use App\Filament\Resources\BusinessSettings\Tables\BusinessSettingsTable;
use App\Filament\Support\MediaRelationManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BusinessSettingResource extends Resource
{
    protected static ?string $model = BusinessSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return __('Business settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Business settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('translations');
    }

    public static function form(Schema $schema): Schema
    {
        return BusinessSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [MediaRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusinessSettings::route('/'),
            'edit' => EditBusinessSetting::route('/{record}/edit'),
        ];
    }
}
