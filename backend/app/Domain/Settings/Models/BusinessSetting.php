<?php

namespace App\Domain\Settings\Models;

use App\Domain\Shared\Concerns\HasWebsiteMedia;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Database\Factories\Settings\BusinessSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class BusinessSetting extends Model implements HasMedia, TranslatableContract
{
    use HasFactory, HasWebsiteMedia, Translatable;

    protected $fillable = ['id', 'email', 'facebook_url', 'whatsapp_number', 'map_url', 'latitude', 'longitude'];

    public $translatedAttributes = ['brand_name', 'tagline', 'address', 'opening_hours', 'quality_highlights'];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    protected static function newFactory()
    {
        return BusinessSettingFactory::new();
    }

    public $incrementing = false;

    public function contactNumbers(): HasMany
    {
        return $this->hasMany(ContactNumber::class)->orderBy('sort_order')->orderBy('id');
    }
}
