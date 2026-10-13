<?php

namespace App\Domain\Content\Models;

use App\Domain\Shared\Concerns\HasWebsiteMedia;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Database\Factories\Content\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Page extends Model implements HasMedia, TranslatableContract
{
    use HasFactory, HasWebsiteMedia, SoftDeletes, Translatable;

    protected $fillable = ['key', 'is_published'];

    public $translatedAttributes = ['title', 'intro', 'body', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    protected static function newFactory()
    {
        return PageFactory::new();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
