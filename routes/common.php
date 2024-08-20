<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api']], function () {
    Route::post('filter-chassis', [CommonController::class, 'filterChassis']);
    Route::post('check-chassis', [CommonController::class, 'checkChassis']);
    Route::get('get-all-district', [CommonController::class, 'getAllDistrict']);
    Route::get('get-all-occupations', [CommonController::class, 'getAllOccupation']);
    Route::get('district-wise-thana', [CommonController::class, 'districtWiseThana']);
    Route::get('get-all-monthly-income', [CommonController::class, 'getAllMonthlyIncome']);
    Route::get('get-all-product-introducing-medias', [CommonController::class, 'productIntroducingMedias']);
    Route::get('get-all-interest-in-product', [CommonController::class, 'getAllInterestInProduct']);
    Route::get('get-all-previously-used-bike', [CommonController::class, 'previouslyUsedBike']);
    Route::get('get-all-previous-bike-cc', [CommonController::class, 'previousBikeCC']);
    Route::get('get-all-previous-bike-usage', [CommonController::class, 'previousBikeUsage']);
    Route::get('get-all-cause-for-buying-new-bike', [CommonController::class, 'causeForBuyingNewBike']);
    Route::get('get-all-emi-installment', [CommonController::class, 'getAllEmiInstallment']);
    Route::get('get-all-emi-bank', [CommonController::class, 'getAllEmiBank']);
    Route::get('get-all-mechanics', [CommonController::class, 'getAllMechanics']);
    Route::get('get-all-exchange-brand', [CommonController::class, 'getAllExchangeBrand']);
    Route::get('get-all-tender-type', [CommonController::class, 'getAllTenderType']);
    Route::get('get-all-customer', [CommonController::class, 'getAllCustomer']);
    Route::get('get-all-region', [CommonController::class, 'getAllRegion']);
    Route::get('get-all-old-bike-brand', [CommonController::class, 'getAllOldBikeBrand']);
    Route::get('get-all-old-bike-model', [CommonController::class, 'getAllOldBikeModel']);
    Route::get('get-user-access', [CommonController::class, 'getUserAccess']);
});
