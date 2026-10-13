<?php

namespace App\Domain\Audit\Services;

use App\Domain\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class RecordAudit
{
    public function execute(Model $record, string $action): void
    {
        $changes = collect($record->getChanges())->except(['password', 'remember_token', 'updated_at'])->all();
        AuditLog::create(['administrator_id' => auth()->id(), 'action' => $action, 'subject_type' => $record->getMorphClass(), 'subject_id' => $record->getKey(),
            'changes' => ['attributes' => $changes, 'actor' => auth()->user()?->email, 'credential_changed' => $record->wasChanged('password')], 'created_at' => now()]);
    }
}
