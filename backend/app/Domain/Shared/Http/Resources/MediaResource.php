<?php

namespace App\Domain\Shared\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->query('locale', 'ar');

        return ['id' => $this->id, 'collection' => $this->collection_name, 'url' => $this->getUrl(),
            'web_url' => $this->hasGeneratedConversion('web') ? $this->getUrl('web') : $this->getUrl(),
            'thumbnail_url' => $this->hasGeneratedConversion('thumbnail') ? $this->getUrl('thumbnail') : $this->getUrl(),
            'alt' => $this->getCustomProperty("alt.$locale", ''), 'locale' => $this->getCustomProperty('locale')];
    }
}
