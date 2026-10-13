<?php

namespace App\Domain\Shared\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class WebsiteResource extends JsonResource
{
    protected function localizedMedia(Request $request): array
    {
        return MediaResource::collection($this->media->filter(fn ($media) => ! $media->getCustomProperty('locale') || $media->getCustomProperty('locale') === $request->query('locale', 'ar')))->resolve($request);
    }
}
