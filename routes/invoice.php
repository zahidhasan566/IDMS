<?php

use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\PrintInvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api']], function () {
    Route::post('calculate-payable-interest', [InvoiceController::class, 'calculatePayableInterest']);
    Route::post('get-all-invoice', [InvoiceController::class, 'getAllInvoice']);
    Route::post('invoice-create', [InvoiceController::class, 'invoiceStore']);
    Route::get('invoice-edit/{InvoiceNo}', [InvoiceController::class, 'invoiceEdit']);
    Route::post('invoice-update', [InvoiceController::class, 'invoiceUpdate']);
    Route::get('invoice-delete/{id}', [InvoiceController::class, 'invoiceDelete']);
    Route::post('invoice/get-chassis-no-info',[PrintInvoiceController::class,'getChassisNoInfo']);
    Route::post('invoice/store-print-invoice',[PrintInvoiceController::class,'storeInvoicePrint']);
    Route::get('get-single-invoice/{InvoiceId}', [InvoiceController::class, 'getSingleInvoice']);
    Route::get('flagship-invoice-print-data/{InvoiceId}', [InvoiceController::class, 'getSingleInvoice']);
});
