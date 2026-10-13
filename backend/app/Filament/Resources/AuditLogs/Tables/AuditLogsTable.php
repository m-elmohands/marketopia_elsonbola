<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('administrator.email')->label(__('Administrator')),
            TextColumn::make('action')->label(__('Action'))->searchable(),
            TextColumn::make('subject_type')->label(__('Entity'))->formatStateUsing(fn ($state) => class_basename($state)),
            TextColumn::make('subject_id')->label(__('Record')),
            TextColumn::make('created_at')->label(__('Date'))->dateTime()->sortable(),
        ])->filters([])->recordActions([Action::make('details')->label(__('Details'))->modalHeading(__('Audit details'))->schema([Textarea::make('details')->label(__('Changes'))->disabled()])->fillForm(fn ($record) => ['details' => json_encode($record->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)])->modalSubmitAction(false)])->defaultSort('created_at', 'desc');
    }
}
