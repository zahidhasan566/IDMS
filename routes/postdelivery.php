<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PostDeliveryController;

Route::group(['middleware' => ['jwt:api'],'prefix' => 'post-delivery'], function () {
    //Spare Part Adjustment
    Route::post('index', [PostDeliveryController::class,'index']);
    Route::get('create', [PostDeliveryController::class,'create']);
    Route::post('store', [PostDeliveryController::class, 'store']);
    Route::get('print/{inquiryId}', [PostDeliveryController::class, 'printData']);
});
