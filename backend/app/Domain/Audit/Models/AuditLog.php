<?php

namespace App\Domain\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['administrator_id', 'action', 'subject_type', 'subject_id', 'changes', 'created_at'];

    protected function casts(): array
    {
        return ['changes' => 'array', 'created_at' => 'datetime'];
    }

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administrator_id')->withTrashed();
    }
}
