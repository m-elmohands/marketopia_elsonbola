<?php

namespace App\Domain\Content\Http\Controllers;

use App\Domain\Content\Actions\GetPublicPage;
use App\Domain\Content\Http\Resources\PageResource;
use App\Domain\Shared\Http\ApiResponse;
use App\Domain\Shared\Http\Requests\PublicContentRequest;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Http\JsonResponse;

class PageController
{
    public function __invoke(PublicContentRequest $request, string $key, GetPublicPage $action, ContentCache $cache): JsonResponse
    {
        return ApiResponse::success($cache->remember('page:'.$key.':'.$request->locale(), fn () => (new PageResource($action->execute($key, $request->locale())))->resolve($request)));
    }
}
