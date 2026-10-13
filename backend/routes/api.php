<?php

use App\Domain\Catalog\Http\Controllers\ProductController;
use App\Domain\Content\Http\Controllers\PageController;
use App\Domain\Settings\Http\Controllers\BusinessSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:public-content')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/details', [ProductController::class, 'details']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/pages/{key}', PageController::class)->whereIn('key', ['home', 'about', 'contact']);
    Route::get('/settings', BusinessSettingController::class);
});
