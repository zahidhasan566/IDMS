<?php

use \App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api'],'prefix' => 'product-promotion'], function () {
    Route::post('list', [PromotionController::class, 'index']);
    Route::get('support-data', [PromotionController::class, 'supportData']);
    Route::get('get-product-by-brand/{brandCode}', [PromotionController::class, 'getProductByBrand']);
    Route::get('get-promotion-info/{promoId}', [PromotionController::class, 'getPromotionInfo']);
    Route::post('create', [PromotionController::class, 'store']);
    Route::post('update/{promoId}', [PromotionController::class, 'update']);
    Route::post('report', [PromotionController::class, 'report']);
    Route::post('top-sheet', [PromotionController::class, 'topSheet']);
    Route::get('load-customer', [PromotionController::class, 'customers']);
});
