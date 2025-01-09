<?php

use App\Http\Controllers\MoneyReceipt\AdvanceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api'],'prefix' => 'money-receipt'], function () {
    Route::post('advance', [AdvanceController::class, 'index']);
    Route::get('get-advance-types',[AdvanceController::class,'getAdvanceTypes']);
    Route::post('create-payment',[AdvanceController::class,'createPayment']);
    Route::get('{moneyRecNo}',[AdvanceController::class,'getById']);
});
