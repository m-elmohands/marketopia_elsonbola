<?php

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSettingTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['brand_name', 'tagline', 'address', 'opening_hours', 'quality_highlights', 'locale'];

    public function parent()
    {
        return $this->belongsTo(BusinessSetting::class, 'business_setting_id');
    }
}
