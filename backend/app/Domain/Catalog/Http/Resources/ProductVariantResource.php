<?php

namespace App\Domain\Catalog\Http\Resources;

use App\Domain\Shared\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;

class ProductVariantResource extends WebsiteResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'weight_kg' => $this->weight_kg, 'media' => $this->localizedMedia($request)];
    }
}
