<?php

use App\Http\Controllers\ClaimWarrantyController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api']], function () {
    Route::post('get-all-claim-warranty', [ClaimWarrantyController::class, 'getAllClaimWarranty']);
    Route::post('claim-warranty-store', [ClaimWarrantyController::class, 'store']);
    Route::post('claim-warranty-update', [ClaimWarrantyController::class, 'update']);
    Route::get('claim-warranty-delete/{id}', [ClaimWarrantyController::class, 'delete']);
    Route::get('get-single-claim-warranty/{InvoiceId}', [ClaimWarrantyController::class, 'getSingleClaimWarranty']);
    Route::get('get-warranty-first-time', [ClaimWarrantyController::class, 'getWarrentyFirstTime']);
    Route::get('job-card-wise-info', [ClaimWarrantyController::class, 'jobCardWiseInfo']);
    Route::get('filter-parts', [ClaimWarrantyController::class, 'filterParts']);
    Route::post('check-parts', [ClaimWarrantyController::class, 'checkParts']);

    //APPROVAL ROUTE
    Route::post('get-all-pending-warranty', [ClaimWarrantyController::class, 'getAllPendingWarranty']);
    Route::get('pending-warranty-edit-and-approved/{WarrantyId}', [ClaimWarrantyController::class, 'pendingWarrantyEditAndApproved']);
    Route::get('claim-warranty-delete/{WarrantyId}', [ClaimWarrantyController::class, 'delete']);
    Route::post('get-all-approved-warranty', [ClaimWarrantyController::class, 'getAllApprovedWarranty']);
    Route::post('get-all-cancel-warranty', [ClaimWarrantyController::class, 'getAllCancelWarranty']);
    Route::post('get-all-claim-warranty-report', [ClaimWarrantyController::class, 'getAllClaimWarrantyReport']);
    Route::get('warranty-print/{DCWarrantyId}', [ClaimWarrantyController::class, 'printWarranty']);

    //SDMS REPORT
    Route::post('sdmsreport/warranty-claim-list', [ClaimWarrantyController::class, 'getSDMSWarrantyClaimList']);
    Route::get('get-all-customer-for-sdms-wwaranty-claim-report', [ClaimWarrantyController::class, 'getCustomer']);

    //warranty-claim-summary-report
    Route::get('warranty-claim-summary-report', [ClaimWarrantyController::class, 'warrantyClaimSummaryReport']);
    Route::get('change-parts-receiving-status', [ClaimWarrantyController::class, 'changePartsReceivingStatus']);
    Route::get('change-warranty-judgement-status', [ClaimWarrantyController::class, 'changeWarrantyJudgementStatus']);
    Route::get('change-warranty-judgement-reject-status', [ClaimWarrantyController::class, 'changeWarrantyJudgementRejectStatus']);
    Route::get('change-factory-qa-status', [ClaimWarrantyController::class, 'changeFactoryQAStatus']);
    Route::get('change-factory-qa-reject-status', [ClaimWarrantyController::class, 'changeFactoryQARejectStatus']);
});
