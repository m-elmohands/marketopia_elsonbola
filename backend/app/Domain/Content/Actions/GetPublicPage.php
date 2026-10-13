<?php

namespace App\Domain\Content\Actions;

use App\Domain\Content\Models\Page;

class GetPublicPage
{
    public function execute(string $key, string $locale): Page
    {
        return Page::published()->where('key', $key)->whereHas('translations', fn ($q) => $q->where('locale', $locale))->with(['translations', 'media'])->firstOrFail();
    }
}
