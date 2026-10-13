<?php

namespace App\Domain\Settings\Http\Controllers;

use App\Domain\Settings\Actions\GetBusinessSettings;
use App\Domain\Settings\Http\Resources\BusinessSettingResource;
use App\Domain\Shared\Http\ApiResponse;
use App\Domain\Shared\Http\Requests\PublicContentRequest;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Http\JsonResponse;

class BusinessSettingController
{
    public function __invoke(PublicContentRequest $request, GetBusinessSettings $action, ContentCache $cache): JsonResponse
    {
        return ApiResponse::success($cache->remember('settings:'.$request->locale(), fn () => (new BusinessSettingResource($action->execute($request->locale())))->resolve($request)));
    }
}
