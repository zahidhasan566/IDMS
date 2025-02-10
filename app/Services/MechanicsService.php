<?php

namespace App\Services;

use App\Models\JobCard\TblDealarMechanics;
use Illuminate\Support\Facades\DB;

class MechanicsService
{
    public $userId, $mechanicsCode;
    public function __construct($userId, $mechanicsCode)
    {
        $this->userId = $userId;
        $this->mechanicsCode = $mechanicsCode;
    }
    public function getList() {
        $query = TblDealarMechanics::join('District','District.DistrictCode','tblDealarMechanics.DistrictCode')
            ->leftJoin('Upazilla','Upazilla.UpazillaCode','tblDealarMechanics.UpazillaCode')
            ->where('ServiceCenterCode',$this->userId)
            ->where('Active','Y');
        if (!empty($this->mechanicsCode)) {
            $query->where('MechanicsCode',$this->mechanicsCode);
        }
        $query->orderBy('MechanicsCode');
        return $query->select('ServiceCenterCode','District.DistrictCode','District.DistrictName','Upazilla.UpazillaCode','Upazilla.UpazillaName',
            'MechanicsCode','MechanicsName','MechanicsPhone',DB::raw("LEFT(JoiningDate,11) AS Joining_Date"),'Address','EducationQualification','MechanicsShopName')->get();
    }
}