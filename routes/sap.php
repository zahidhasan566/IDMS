<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'jwt:api'], function () {
    Route::post('sap-product-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapProduct']);
    Route::post('sap-customer-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapCustomer']);
});



//Route::group(['middleware' => ['jwt:api']], function () {
//
//});
