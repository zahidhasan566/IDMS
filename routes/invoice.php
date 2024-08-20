<?php

use App\Http\Controllers\Invoice\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api']], function () {
    Route::post('calculate-payable-interest', [InvoiceController::class, 'calculatePayableInterest']);
    Route::post('get-all-invoice', [InvoiceController::class, 'getAllInvoice']);
    Route::post('invoice-create', [InvoiceController::class, 'invoiceStore']);
    Route::get('invoice-delete/{id}', [InvoiceController::class, 'invoiceDelete']);
    Route::get('get-single-invoice/{InvoiceId}', [InvoiceController::class, 'getSingleInvoice']);
});
