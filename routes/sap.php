<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::post('sap-product-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapProduct']);


//Route::group(['middleware' => ['jwt:api']], function () {
//
//});
