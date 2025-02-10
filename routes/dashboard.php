<?php

use App\Http\Controllers\Logistics\DealerReceiveController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\DashboardController;

Route::group(['middleware' => 'jwt:api','prefix' => 'dashboard'], function () {

    Route::post('receivables',[DashboardController::class,'receivables']);
    Route::get('receivable-by-id/{invoiceId}',[DashboardController::class,'getReceivableById']);
    Route::post('do-survey',[DashboardController::class,'storeSurvey']);
    Route::post('receivables/store',[DashboardController::class,'storeReceivable']);
    Route::get('invoice-receive-survey-data',[DashboardController::class,'invoiceReceiveSurveyData']);
    //order
    Route::post('orders',[DashboardController::class,'getAllMyOrders']);
    Route::get('get-order-details/{orderNo}',[DashboardController::class,'getOrderDetails']);
    //logistics
    Route::post('logistics', [DealerReceiveController::class,'getAllPendingDocument']);
    Route::post('update-document', [DealerReceiveController::class,'updateLogisticsDocument']);

    //pendingOrders
    Route::post('pending-orders', [DashboardController::class,'pendingOrders']);
    Route::post('pending-orders/store',[DashboardController::class,'storeApproved']);
    Route::get('edit-approve',[DashboardController::class,'editApproved']);
});