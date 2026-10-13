<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('action')->label(__('Action'))->disabled()]);
    }
}
