<?php

namespace App\Domain\Catalog\Http\Resources;

use App\Domain\Shared\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;

class ProductResource extends WebsiteResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translate($request->query('locale', 'ar'), false);

        return ['id' => $this->id, 'slug' => $this->slug, 'name' => $translation?->name, 'short_description' => $translation?->short_description, 'description' => $translation?->description,
            'meta_title' => $translation?->meta_title, 'meta_description' => $translation?->meta_description, 'is_featured' => $this->is_featured,
            'variants' => ProductVariantResource::collection($this->variants)->resolve($request), 'media' => $this->localizedMedia($request)];
    }
}
