<?php

use App\Http\Controllers\PromoCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('promo-codes')->group(function () {
    Route::post('/validate', [PromoCodeController::class, 'validate']);
    Route::post('/', [PromoCodeController::class, 'store']);
});
