<?php

namespace App\Services;

use App\Models\JobCard\TblDealarAffiliator;
use Illuminate\Support\Facades\DB;

class AffiliatedAgentService
{
    public $affiliatorCode;
    public function __construct($affiliatorCode)
    {
        $this->affiliatorCode = $affiliatorCode;
    }

    public function getList()
    {
        $query = TblDealarAffiliator::join('District','District.DistrictCode','tblDealarAffiliator.DistrictCode')
            ->where('tblDealarAffiliator.Active','Y');
        if (!empty($affiliatorCode)) {
            $query->where('tblDealarAffiliator.AffiliatorCode',$this->affiliatorCode);
        }
        return $query->select('tblDealarAffiliator.DistrictCode','District.DistrictName','AffiliatorCode','AffiliatorName','AffiliatorPhone as Mobile',
            DB::raw("LEFT(JoiningDate,11) AS JoiningDate"),'Address','EducationQualification','AffiliatorShopName')->get();
    }
}