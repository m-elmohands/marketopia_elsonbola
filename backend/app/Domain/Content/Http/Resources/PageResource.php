<?php

namespace App\Domain\Content\Http\Resources;

use App\Domain\Shared\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;

class PageResource extends WebsiteResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translate($request->query('locale', 'ar'), false);

        return [
            'key' => $this->key,
            'title' => $t?->title,
            'intro' => $t?->intro,
            'body' => $t?->body,
            'meta_title' => $t?->meta_title,
            'meta_description' => $t?->meta_description,
            'media' => $this->localizedMedia($request),
        ];
    }
}
