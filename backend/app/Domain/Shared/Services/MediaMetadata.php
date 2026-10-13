<?php

namespace App\Domain\Shared\Services;

use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class MediaMetadata
{
    public function alternatives(?Model $record, callable $get): array
    {
        $alt = [];
        foreach (['ar', 'en'] as $locale) {
            $text = $get("$locale.name") ?? $get("$locale.title") ?? $get("$locale.brand_name") ?? '';
            if ($record instanceof ProductVariant) {
                $text = trim(($record->product?->translate($locale, false)?->name ?? '').' '.$get('weight_kg').' kg');
            }
            $alt[$locale] = $text;
        }

        return ['alt' => $alt];
    }
}
