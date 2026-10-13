<?php

namespace App\Domain\Settings\Http\Resources;

use App\Domain\Shared\Http\Resources\WebsiteResource;
use Illuminate\Http\Request;

class BusinessSettingResource extends WebsiteResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translate($request->query('locale', 'ar'), false);

        return ['brand_name' => $t?->brand_name, 'tagline' => $t?->tagline, 'address' => $t?->address, 'opening_hours' => $t?->opening_hours,
            'quality_highlights' => array_values(array_filter(explode("\n", $t?->quality_highlights ?? ''))),
            'email' => $this->email, 'facebook_url' => $this->facebook_url, 'whatsapp_number' => $this->whatsapp_number, 'map_url' => $this->map_url,
            'latitude' => $this->latitude, 'longitude' => $this->longitude, 'contact_numbers' => $this->contactNumbers->map(fn ($number) => [
                'phone' => $number->phone,
                'is_primary' => $number->is_primary,
            ])->all(), 'media' => $this->localizedMedia($request)];
    }
}
