<?php

use \App\Http\Controllers\Invoice\InvoiceSparePartsController;
use Illuminate\Support\Facades\Route;
Route::group(['middleware' => ['jwt:api']], function () {
    Route::post('invoice-spare-parts/list', [InvoiceSparePartsController::class, 'index']);
    Route::get('invoice-spare-parts/get-supporting-data', [InvoiceSparePartsController::class, 'getSparePartsSupportingData']);
    Route::post('invoice-spare-parts/search', [InvoiceSparePartsController::class, 'filterSpareParts']);
    Route::post('invoice-spare-parts/stock-check', [InvoiceSparePartsController::class, 'stockCheck']);
    Route::post('invoice-spare-parts/checkout', [InvoiceSparePartsController::class, 'createInvoice']);
    Route::post('invoice-spare-parts/get-data-by-id', [InvoiceSparePartsController::class, 'getInvoiceData']);
    Route::post('invoice-spare-parts/get-customer-by-chassis', [InvoiceSparePartsController::class, 'getCustomerByChassis']);
    Route::post('invoice-spare-parts/return', [InvoiceSparePartsController::class, 'returnInvoice']);
    Route::get('invoice-spare-parts/print/{invoiceId}', [InvoiceSparePartsController::class, 'sparePartsInvoicePrint']);

});

Route::group(['middleware' => ['jwt:api'],'prefix' => 'invoice-spare-parts'], function () {
    //Spare Part Adjustment
    Route::post('adjustment-list', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class,'index']);
    Route::get('export-adjustment-demo-excel', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class,'getDemoExcelFile']);
    Route::post('adjustment-store', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class, 'adjustmentStore']);
    Route::post('adjustment-return', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class, 'returnAdjustmentInvoice']);
    Route::get('get-adjustment-dealer-data', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class, 'getAdjustmentSupportingData']);
    Route::get('get/existing-adjustment/{adjustmentInvoiceNo}', [\App\Http\Controllers\Invoice\InvoiceSparePartsAdjustmentController::class, 'getExistingAdjustment']);
});
