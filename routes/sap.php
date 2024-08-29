<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::post('sap-product-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapProduct']);
Route::post('sap-product-customer', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapCustomer']);


//Route::group(['middleware' => ['jwt:api']], function () {
//
//});
