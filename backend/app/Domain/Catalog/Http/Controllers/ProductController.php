<?php

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Http\Requests\ProductDetailsRequest;
use App\Domain\Catalog\Http\Resources\ProductResource;
use App\Domain\Catalog\Services\BrowseProducts;
use App\Domain\Shared\Http\ApiResponse;
use App\Domain\Shared\Http\Requests\PublicContentRequest;
use App\Domain\Shared\Services\ContentCache;
use Illuminate\Http\JsonResponse;

class ProductController
{
    public function index(PublicContentRequest $request, BrowseProducts $browse, ContentCache $cache): JsonResponse
    {
        $payload = $cache->remember('products:'.$request->locale().':'.http_build_query($request->validated()), function () use ($request, $browse) {
            $products = $browse->execute($request->validated(), $request->locale());

            return [
                'data' => ProductResource::collection($products)->resolve($request),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ],
            ];
        });

        return ApiResponse::success($payload['data'], $payload['meta']);
    }

    public function details(ProductDetailsRequest $request, BrowseProducts $browse, ContentCache $cache): JsonResponse
    {
        $id = (int) $request->validated('id');
        $data = $cache->remember('product:id:'.$id.':'.$request->locale(), fn () => (new ProductResource($browse->findById($id, $request->locale())))->resolve($request));

        return ApiResponse::success($data);
    }

    public function show(PublicContentRequest $request, string $slug, BrowseProducts $browse, ContentCache $cache): JsonResponse
    {
        return ApiResponse::success($cache->remember('product:'.$slug.':'.$request->locale(), fn () => (new ProductResource($browse->query($request->locale())->where('slug', $slug)->firstOrFail()))->resolve($request)));
    }
}
