<?php

namespace App\Services;

use App\Models\Business;
use App\Models\UserBusiness;
use Illuminate\Support\Facades\Auth;

class BusinessService
{
    public static function list() {
        $business = Business::where('Status','Y')->get();
        return $business;
    }

    public static function userBusiness()
    {
        return UserBusiness::select('Business.Business as Business','Business.BusinessName as BusinessName')
            ->join('Business','Business.Business','UserBusiness.Business')
            ->where('UserBusiness.Business','C')->first();
    }
}