<?php

namespace App\Domain\Shared\Services;

use App\Domain\Shared\Jobs\RevalidateFrontend;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentCache
{
    public function remember(string $key, callable $callback): array
    {
        $revision = Cache::rememberForever('website:revision', fn () => (string) Str::uuid());

        return Cache::remember("website:$revision:$key", config('website.cache_ttl'), $callback);
    }

    public function invalidate(): void
    {
        DB::afterCommit(function (): void {
            Cache::forever('website:revision', (string) Str::uuid());
            if (config('website.revalidation_url') && config('website.revalidation_token')) {
                RevalidateFrontend::dispatch();
            }
        });
    }
}
