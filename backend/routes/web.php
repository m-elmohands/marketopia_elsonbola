<?php

use App\Domain\Administration\Http\Controllers\AdminLocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));
Route::post('/admin/locale', AdminLocaleController::class)->middleware('auth')->name('admin.locale');
