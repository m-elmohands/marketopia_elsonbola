<?php

namespace App\Domain\Shared\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasWebsiteMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        foreach (['cover', 'packaging', 'hero', 'logo'] as $name) {
            $this->addMediaCollection($name)->useDisk('public')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])->singleFile();
        }
        $this->addMediaCollection('gallery')->useDisk('public')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->fit(Fit::Max, 400, 400)->format('webp')->queued();
        $this->addMediaConversion('web')->fit(Fit::Max, 1600, 1600)->format('webp')->queued();
    }
}
