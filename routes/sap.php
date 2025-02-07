<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'jwt:api'], function () {
    Route::get('get-dms-bike-order', [\App\Http\Controllers\Sap\InboundController::class, 'getBikeOrder']);
    Route::get('get-dms-spare-parts-order', [\App\Http\Controllers\Sap\InboundController::class, 'getSparePartsOrder']);
    Route::get('get-dms-payment', [\App\Http\Controllers\Sap\InboundController::class, 'getPaymentInfo']);
    Route::post('sap-product-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapProduct']);
    Route::post('sap-customer-add', [\App\Http\Controllers\Sap\CommonSapController::class, 'storeSapCustomer']);
    Route::post('prebooking-add', [\App\Http\Controllers\Sap\PrebookingController::class, 'storePreBookingCustomer']);
    Route::post('sap-invoice-add', [\App\Http\Controllers\Sap\SapInvoiceController::class, 'storeSapInvoice']);
    Route::post('sap-brta-invoice-add', [\App\Http\Controllers\Sap\SapInvoiceController::class, 'storeFlagShipInvoice']);


});
