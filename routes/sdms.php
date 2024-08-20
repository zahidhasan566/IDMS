<?php

use \App\Http\Controllers\SdmsReportController;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => ['jwt:api'],'prefix' => 'sdms-report'], function () {
    Route::post('invoice-list', [SdmsReportController::class, 'invoiceList']);
    Route::get('invoice-list/{invoiceNo}',[SdmsReportController::class,'invoiceDetails']);
    Route::post('customer-ledger',[SdmsReportController::class,'customerLedger']);
    Route::post('customer-wise-product-sold',[SdmsReportController::class,'customerWiseProductSold']);
    Route::post('day-wise-sales-summary-report',[SdmsReportController::class,'dayWiseSalesSummary']);
    Route::post('dealer-offer-list',[SdmsReportController::class,'dealerOfferList']);
    Route::post('upload-offer-list',[SdmsReportController::class,'uploadOfferList']);
});
