<?php

use App\Http\Controllers\Settings\CustomerController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\CommonController;
use \App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use \App\Http\Controllers\SettingController;
use App\Http\Controllers\Orders\BikeController;
use App\Http\Controllers\Stock\StockController;
use \App\Http\Controllers\DealerUsersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Logistics\ChallanController;
use App\Http\Controllers\Orders\SparePartsController;
use App\Http\Controllers\TestRide\TestRiderController;
use App\Http\Controllers\JobCard\UserCustomerController;
use App\Http\Controllers\Logistics\LostDocumentController;
use App\Http\Controllers\iHelpBD\SendCallRequestController;
use App\Http\Controllers\TestRide\TestRideAgentsController;
use App\Http\Controllers\Logistics\DealerDocumentController;
use App\Http\Controllers\Evaluation\EvaluationReportController;
use App\Http\Controllers\Evaluation\EvaluationSalesController;
use App\Http\Controllers\JobCard\PaidServiceScheduleController;
use App\Http\Controllers\JobCard\FreeServiceFollowUpController;
use App\Http\Controllers\Evaluation\EvaluationForService4PController;

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::group(['middleware' => 'jwt:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::get('app-supporting-data', [SettingController::class, 'appSupportingData'])->middleware('mod');

Route::group(['middleware' => ['jwt:api']], function () {
    Route::get('encoded-result/{param}',[CommonController::class,'encode']);
    Route::get('decoded-result/{param}',[CommonController::class,'decode']);

    /**
     * USER MODULE START
     */
    // ADMIN USERS
    Route::group(['prefix' => 'user'],function () {
        Route::post('add', [UserController::class, 'store']);
        Route::post('update', [UserController::class, 'update']);
        Route::post('list', [UserController::class, 'index']);

        //Branch List
        Route::post('branchList', [UserController::class, 'branchList']);
        //get Banks List
        Route::get('get-banks', [UserController::class, 'getBanks']);
        //save Branch
        Route::post('saveBranch', [UserController::class, 'saveBranch']);
        //Update Branch
        Route::post('updateBranch', [UserController::class, 'updateBranch']);
        //User Modal Data
        Route::get('modal',[CommonController::class,'userModalData']);
        Route::get('get-user-info/{staffId}',[UserController::class,'getUserInfo']);
        Route::get('check-user-update',[UserController::class,'userPasswordCheck']);
        Route::post('reset-password',[UserController::class,'updatePassword']);
        Route::post('password-change',[UserController::class,'passwordChange']);
    });
    Route::group(['prefix' => 'dashboard'],function () {
        Route::get('current-balance', [\App\Http\Controllers\DashboardController::class, 'getCurrenBalanceData']);
    });

    //DEALER USERS
    Route::group(['prefix' => 'dealer'],function () {
        Route::post('users', [DealerUsersController::class, 'index']);
        Route::post('user/create', [DealerUsersController::class, 'store']);
        Route::post('user/update/{userId}', [DealerUsersController::class, 'update']);
        Route::get('support-data', [DealerUsersController::class, 'supportData']);

        //User Modal Data
        Route::get('modal',[CommonController::class,'userModalData']);
        Route::get('get-user-info/{staffId}',[UserController::class,'getUserInfo']);
        Route::post('reset-password',[UserController::class,'updatePassword']);
        Route::post('password-change',[UserController::class,'passwordChange']);
    });

    /**
     * USER MODULE END
     */

    //Orders
    Route::group(['prefix' => 'orders'],function () {
        //Bike Order
        Route::get('bike-list', [BikeController::class,'index']);
        Route::post('store-bike', [BikeController::class,'storeBikeOrder']);
        Route::get('search-product', [CommonController::class,'bikeList']);
        Route::get('get-bike-by-product-code', [CommonController::class,'getBikeByProductCode']);

    //Spare Parts
        Route::get('spare-parts-list', [SparePartsController::class,'index']);
        Route::get('search-spare-parts/{query}', [SparePartsController::class,'searchSpareParts']);
        Route::get('search-parts', [CommonController::class,'searchParts']);
        Route::get('get-parts-by-product-code', [CommonController::class,'getPartsByProductCode']);
        Route::get('export-order-excel', [SparePartsController::class,'getExcelFile']);
        Route::post('spare-parts-import-data', [SparePartsController::class,'importExcelFile']);
        Route::post('store-parts', [SparePartsController::class,'storeSparePartsOrder']);
    });

     //iHelpBD
     Route::group(['prefix' => 'iHelpBD'],function () {
        Route::post('send-yamaha-call', [SendCallRequestController::class,'sendYamahaCall']);
        Route::post('send-foton-call', [SendCallRequestController::class,'sendFotonCall']);
        Route::post('send-agrimotors-call', [SendCallRequestController::class,'sendAgrimotorsCall']);
     });

    //payment
    Route::group(['prefix' => 'payment'],function () {
        Route::get('credit-payment-list', [PaymentController::class,'index']);
        Route::post('store-credit-payment', [PaymentController::class,'store']);
        Route::get('get-customer-list', [PaymentController::class,'getCustomerCode']);
        Route::get('get-all-customer', [PaymentController::class,'getCustomerList']);
        Route::get('get-customer-wise-business', [CommonController::class,'getcustomerWiseBusiness']);
        Route::get('get-all-bank', [CommonController::class,'getAllBank']);
        Route::get('get-sales-type', [PaymentController::class,'getSalesType']);
    });
    //stock
    Route::group(['prefix' => 'stock'],function () {
        Route::post('spare-parts-stock', [StockController::class,'sparePartsindex']);
        Route::post('bike-stock', [StockController::class,'bikeIndex']);
        Route::post('msl-list', [StockController::class,'mslIndex']);
        Route::post('store-allocation', [StockController::class,'storeRackAllocation']);
        Route::post('allocation', [StockController::class,'allocationList']);
        Route::get('get-all-stock-product/{productCode}', [StockController::class,'getAllStockProduct']);
        Route::post('get-spare-parts-receive-history', [StockController::class,'getSparePartsHistory']);

    });

    Route::group(['prefix' => 'reports'],function () {
        Route::get('supporting-data',           [ReportController::class,'getReportSupportingData']);
        Route::get('invoice-receive-survey-report-supporting-data',[ReportController::class,'getInvoiceReceiveSurevyReportSupportingData']);
        Route::get('region-data',               [ReportController::class,'getUserWiseRegionData']);
        Route::post('sales-summary',            [ReportController::class,'getSalesSummeryReport']);
        Route::post('bike-sales',               [ReportController::class,'getBikeSalesReport']);
        Route::post('spare-parts-sales',        [ReportController::class,'getSparePartsSalesReport']);
        Route::post('spare-parts-sales/export', [ReportController::class,'getSparePartsSalesReportExcel']);
        Route::post('spare-parts-affiliator',   [ReportController::class,'getSparePartsAffiliatorSalesReport']);
        Route::post('bike-order',               [ReportController::class,'getBikeOrderReport']);
        Route::post('spare-parts-order',        [ReportController::class,'getSparePartsReport']);
        Route::post('bike-stock',               [ReportController::class,'getBikeStockReport']);
        Route::post('spare-parts-stock',        [ReportController::class,'getSparePartsStockReport']);
        Route::post('estimated-free-service',   [ReportController::class,'getEstimatedFreeService']);
        Route::post('estimated-paid-service',   [ReportController::class,'getEstimatedPaidService']);
        Route::post('claim-warranty',           [ReportController::class,'getClaimWarranty']);
        Route::post('service-ratio',            [ReportController::class,'getServiceRatio']);
        Route::post('customer-csi-summary',     [ReportController::class,'getCustomerCSISummary']);
        Route::post('opening-closeing-stock',   [ReportController::class,'getOpeningCloseingStock']);
        Route::post('customer-bike-sales-feedBack',        [ReportController::class,'getCustomerBikeSalesFeedBack']);
        Route::post('invoice-survey-report-data',          [ReportController::class,'getInvoiceSurveyReportData']);
        Route::post('dealer-invoice-survey-report-data',   [ReportController::class,'getDealerInvoiceSurveyReportData']);
        Route::post('service-summary',                     [ReportController::class,'getServiceSummaryReport']);
        Route::post('scrap-products',        [ReportController::class,'getScrapProductsReport']);
    });
    //JOB CARD
    Route::group(['prefix' => 'jobCard'],function () {
        //Bay
        Route::post('bay-list', [\App\Http\Controllers\JobCard\BayController::class,'index']);
        Route::get('bay-supporting-data', [\App\Http\Controllers\JobCard\BayController::class,'getSupportingData']);
        Route::post('bay-add', [\App\Http\Controllers\JobCard\BayController::class,'store']);
        Route::get('get/bay/modal/{bayCode}/{serviceCenterCode}',[\App\Http\Controllers\JobCard\BayController::class,'existingBayInfo']);
        Route::post('bay-update', [\App\Http\Controllers\JobCard\BayController::class,'updateBay']);

        //Work
        Route::post('work-list', [\App\Http\Controllers\JobCard\WorkController::class,'index']);
        Route::post('work-add', [\App\Http\Controllers\JobCard\WorkController::class,'store']);
        Route::get('get/work/modal/{workCode}',[\App\Http\Controllers\JobCard\WorkController::class,'existingWorkInfo']);
        Route::post('work-update', [\App\Http\Controllers\JobCard\WorkController::class,'updateWork']);

        //Technician
        Route::post('technician-list', [\App\Http\Controllers\JobCard\TechnicianController::class,'index']);
        Route::get('technician-supporting-data', [\App\Http\Controllers\JobCard\TechnicianController::class,'getSupportingData']);
        Route::get('technician-resign-supporting-data', [\App\Http\Controllers\JobCard\TechnicianController::class,'getResignSupportingData']);
        Route::get('technician-training-supporting-data/{technicianCode}', [\App\Http\Controllers\JobCard\TechnicianController::class,'getTrainingSupportingData']);
        Route::post('technician-add', [\App\Http\Controllers\JobCard\TechnicianController::class,'store']);
        Route::get('get/technician/modal/{technicianCode}',[\App\Http\Controllers\JobCard\TechnicianController::class,'existingTechnicianInfo']);
        Route::post('technician-update', [\App\Http\Controllers\JobCard\TechnicianController::class,'updateTechnician']);
        Route::post('technician-resignation', [\App\Http\Controllers\JobCard\TechnicianController::class,'resignTechnician']);
        Route::post('technician_training_add', [\App\Http\Controllers\JobCard\TechnicianController::class,'storeTechnicianTraining']);
        Route::get('technician-check-bay-data/{serviceCenterCode}', [\App\Http\Controllers\JobCard\TechnicianController::class,'getBaySupportingData']);


        //Job Card
        Route::post('jobCard-list', [\App\Http\Controllers\JobCard\JobCardController::class,'index']);
        Route::get('check-chassis-no/{chassisNo}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkChassisNo']);
        Route::get('check-last-service-history/{chassisNo}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkLastServiceHistory']);
        Route::get('check-spare-parts/{productCode}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkSpareParts']);
        Route::get('check-services/{serviceName}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkService']);
        Route::get('supporting-data', [\App\Http\Controllers\JobCard\JobCardController::class,'getSupportingData']);
        Route::post('job-add', [\App\Http\Controllers\JobCard\JobCardController::class,'store']);
        Route::post('job-update', [\App\Http\Controllers\JobCard\JobCardController::class,'updateJobCard']);
        Route::post('job-close', [\App\Http\Controllers\JobCard\JobCardController::class,'jobClose']);
        Route::get('job-display-board', [\App\Http\Controllers\JobCard\JobCardController::class,'jobCardDisplay']);

        Route::get('get/jobCard/modal/{jobCardNo}',[\App\Http\Controllers\JobCard\JobCardController::class,'existingJobCard']);
        Route::post('jobCard-update', [\App\Http\Controllers\JobCard\JobCardController::class,'updateTechnician']);
        Route::get('job-report-supporting-data',[\App\Http\Controllers\JobCard\AllJobCardReportController::class,'getJobReportSupportingData']);
        Route::post('job-card-report',[\App\Http\Controllers\JobCard\AllJobCardReportController::class,'getJobReportData']);
        Route::post('job-card-csi-data',[\App\Http\Controllers\JobCard\AllJobCardReportController::class,'getJobCSIData']);
        Route::post('job-card-booking-report',[\App\Http\Controllers\JobCard\AllJobCardReportController::class,'getBookingReportData']);
        Route::get('csi-supporting-data', [\App\Http\Controllers\JobCard\JobCardController::class,'csiSupportingData']);
        Route::post('csi-add-data', [\App\Http\Controllers\JobCard\JobCardController::class,'csiAddData']);
        //Job Card Estimation List
        Route::post('estimation-list', [\App\Http\Controllers\JobCard\JobCardEstimationController::class,'index']);
        Route::post('job-estimation-add', [\App\Http\Controllers\JobCard\JobCardEstimationController::class,'store']);
        Route::get('get/estimation-job-card/{estimationNo}', [\App\Http\Controllers\JobCard\JobCardEstimationController::class,'existingEstimation']);
        Route::get('check-service-history/{chassisNo}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkServiceHistory']);
        Route::get('check-service-history-problem-details/{jobCardNo}/{dealerCode}', [\App\Http\Controllers\JobCard\JobCardController::class,'checkServiceHistoryProblemDetails']);
        Route::post('update-service-history', [\App\Http\Controllers\JobCard\JobCardController::class,'updateServiceHistory']);

        //Technician Report
        Route::get('technician-report-supporting-data',[\App\Http\Controllers\JobCard\TechnicianController::class,'getReportSupportingData']);
        Route::post('technician-report',[\App\Http\Controllers\JobCard\TechnicianController::class,'getReportData']);

        //Free service FollowUp
        Route::post('free-service-followup',[FreeServiceFollowUpController::class,'index']);
        Route::post('free-service-followup/update',[FreeServiceFollowUpController::class,'update']);
        //PaidServiceSchedule
        Route::post('paid-service-schedule',[PaidServiceScheduleController::class,'index']);
        Route::post('paid-service-schedule/update',[PaidServiceScheduleController::class,'update']);

       // user customer
        Route::post('user-customer',[UserCustomerController::class,'index']);
        Route::post('user-customer/store',[UserCustomerController::class,'store']);
        Route::post('user-customer/delete',[UserCustomerController::class,'delete']);
        Route::get('get-all-roles',[UserCustomerController::class,'getAllRole']);
    });

    Route::group(['prefix' => 'logistics'],function () {
        //Inquiry
        Route::post('dealer-document-report', [DealerDocumentController::class,'index']);
        Route::post('dealer-document', [DealerDocumentController::class,'store']);
        Route::post('get-invoice-details', [DealerDocumentController::class,'getInvoiceDetails']);
        Route::post('store-document', [DealerDocumentController::class,'storeDocument']);

        Route::post('get-lost-invoice', [LostDocumentController::class,'getLostInvoice']);
        Route::post('update-lost-invoice-status', [LostDocumentController::class,'updateLostInvoiceStatus']);
        Route::post('sed-lost-invoice-copy', [LostDocumentController::class,'sendLostInvoiceCopy']);
        

        Route::post('get-lost-invoice-details', [LostDocumentController::class,'getLostInvoiceDetails']);
        Route::post('submit-lost-invoice-details', [LostDocumentController::class,'storeLostInvoiceDetails']);

        Route::post('get-customer-list', [LostDocumentController::class,'customerList']);
        Route::post('get-lost-document-report', [LostDocumentController::class,'lostDocument']);

        //CHALLAN LIST ROUTE
        Route::post('get-all-challan-list', [ChallanController::class,'index']);
        Route::post('upload-challan', [ChallanController::class,'storeChallan']);
        Route::post('update-challan', [ChallanController::class,'updateChallan']);

        //BRTA REGISTRATION
        Route::post('brta-registration-list', [\App\Http\Controllers\Logistics\BrtaRegistrationController::class,'index']);
        Route::get('get/brta-registration-status/modal/{brtaRegistrationStatusId}', [\App\Http\Controllers\Logistics\BrtaRegistrationController::class,'getExistingBrtaRegistrationStatus']);
        Route::post('update-brta-registration-list', [\App\Http\Controllers\Logistics\BrtaRegistrationController::class,'updateBrtaRegistrationStatus']);

        //Receive Report
        Route::get('receive-report-supporting-data',[\App\Http\Controllers\Logistics\ReceiveReportController::class,'getJobReportSupportingData']);
        Route::post('receive-report',[\App\Http\Controllers\Logistics\ReceiveReportController::class,'getReceiveReportData']);
        Route::post('receive-report-summary',[\App\Http\Controllers\Logistics\ReceiveReportController::class,'getReceiveSummaryReportData']);

        });


    //Inquiry
    Route::group(['prefix' => 'inquiry'],function () {
        //Inquiry
        Route::post('follow-up-list', [\App\Http\Controllers\Inquiry\InquiryProgressAndFollowUpController::class,'index']);
        Route::get('supporting-data', [\App\Http\Controllers\Inquiry\InquiryProgressAndFollowUpController::class,'getSupportingData']);
        Route::get('search-product/{product}', [\App\Http\Controllers\Inquiry\InquiryProgressAndFollowUpController::class,'searchProduct']);
        Route::post('progress-card-add', [\App\Http\Controllers\Inquiry\InquiryProgressAndFollowUpController::class,'addProgressCard']);
        Route::post('update-follow-up', [\App\Http\Controllers\Inquiry\InquiryProgressAndFollowUpController::class,'updateFollowUp']);
        Route::get('load-supportingData', [\App\Http\Controllers\inquiry\InquiryFollowUpReportController::class, 'supportingData']);
        Route::post('conversion-summary-report', [\App\Http\Controllers\inquiry\InquiryFollowUpReportController::class, 'report']);
    });
    //Inquiry
    Route::group(['prefix' => 'test-ride'],function () {
        //Inquiry
        Route::post('agent', [TestRideAgentsController::class,'index']);
        Route::get('get-all-list', [TestRideAgentsController::class,'getAllList']);
        Route::post('store', [TestRideAgentsController::class,'store']);
        Route::get('edit/{agentId}',[TestRideAgentsController::class,'show']);
        Route::post('update', [TestRideAgentsController::class,'update']);

        Route::post('rider', [TestRiderController::class,'index']);
        Route::get('get-all-agents', [TestRiderController::class,'getAllAgents']);
        Route::post('store-rider', [TestRiderController::class,'store']);
        Route::get('edit-rider/{rideId}',[TestRiderController::class,'show']);
        Route::post('update-rider', [TestRiderController::class,'update']);
    });
    //transport-notification
    Route::group(['prefix' => 'transport-notification'],function () {

        Route::post('notification-list', [NotificationController::class,'index']);
        Route::get('get-transport-list', [NotificationController::class,'getTransportList']);
        Route::get('get-all-dealer', [NotificationController::class,'getAllDealer']);
        Route::get('get-challan-information', [NotificationController::class,'getChallanInformation']);
        Route::post('store', [NotificationController::class,'store']);
        Route::get('edit/{notificationID}',[NotificationController::class,'show']);
    });

//evalution
    Route::group(['prefix' => 'evaluation'],function () {

        Route::post('service-sales',                    [EvaluationSalesController::class,'index']);
        Route::post('service-details',                  [EvaluationSalesController::class,'salesDetails']);
        Route::post('store',                            [EvaluationSalesController::class,'store']);
        Route::get('get-all-dealer',                    [EvaluationSalesController::class,'getAllDealer']);
        Route::post('region-data',                      [EvaluationSalesController::class,'getUserRegion']);
        Route::post('service-4p-list',                  [EvaluationForService4PController::class,'index']);
        Route::get('get-service-4p-supporting-data',    [EvaluationForService4PController::class,'getSupportingData']);
        Route::post('report',                           [EvaluationReportController::class,'index']);
        Route::post('details-report',                   [EvaluationReportController::class,'detailsReport']);
        Route::post('service4p-store',                   [EvaluationForService4PController::class,'store4p']);
        Route::get('get-service-4p-part-2-supporting-data/{evaluationId}', [EvaluationForService4PController::class,'getSupportingDataPart2']);
        Route::post('service4p-store-part2',                    [EvaluationForService4PController::class,'store4pPart2']);
//        Route::post('service-4p-list',               [EvaluationForService4PController::class,'index']);

    });
    Route::group(['prefix' => 'settings'],function () {
        Route::get('product-supporting-data',                    [\App\Http\Controllers\Settings\ProductController::class,'supportingData']);
        Route::post('product-list',                    [\App\Http\Controllers\Settings\ProductController::class,'index']);
        Route::post('product-store',                    [\App\Http\Controllers\Settings\ProductController::class,'store']);

        //CUSTOMER
        Route::post('get-all-customer',                  [CustomerController::class,'index']);
        Route::post('customer-store',                    [CustomerController::class,'store']);
        Route::post('customer-update',                    [CustomerController::class,'update']);
        Route::get('customer-edit/{CustomerCode}',                     [CustomerController::class,'edit']);
        Route::get('get-all-customer-type',              [CustomerController::class,'getAllCustomerType']);

        Route::get('get/product/modal/{productCode}',[\App\Http\Controllers\Settings\ProductController::class,'getExistingProduct']);
        Route::post('product-update',                    [\App\Http\Controllers\Settings\ProductController::class,'updateProduct']);

    });

    Route::group(['prefix' => 'prebook'],function () {
        Route::get('supporting-data',   [\App\Http\Controllers\Sap\PrebookingController::class,'getPreBookSupportingData']);
        Route::post('prebook-report-data',   [\App\Http\Controllers\Sap\PrebookingController::class,'getPreBookingReport']);
    });


});


//Route::get('test-mail/{email}',[ReportController::class,'sendPHPMailerEmail']);