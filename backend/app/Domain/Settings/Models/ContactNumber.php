<?php

namespace App\Domain\Settings\Models;

use Database\Factories\Settings\ContactNumberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactNumber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['business_setting_id', 'phone', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'sort_order' => 'integer'];
    }

    public function businessSetting()
    {
        return $this->belongsTo(BusinessSetting::class);
    }

    protected static function newFactory()
    {
        return ContactNumberFactory::new();
    }
}
