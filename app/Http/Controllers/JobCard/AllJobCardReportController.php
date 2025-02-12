<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblJobStatus;
use App\Models\JobCard\TblJobType;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AllJobCardReportController extends Controller
{
   use CommonTrait;
    public function getJobReportSupportingData(){
        $user  = $this->loadCustomer();
        $allJobType  = TblJobType::select('JobTypeName as text','Id as value',)
            ->where('ParentId','=',0)
            ->where('Active','Y')->orderBy('ReportOrder','asc')->get();
        $tblJobStatus  =  TblJobStatus::select('TblJobStatus.StatusName as text','TblJobStatus.StatusCode as value')->where('Active','Y')->get();
        return response()->json([
            'user' => $user,
            'tblJobStatus' => $tblJobStatus,
            'allJobType' => $allJobType,
        ]);
    }
    public function getJobReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $JobStatus = $request->JobStatus;
        $JobType = $request->JobType;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $roleId = Auth::user()->RoleId;
        $userId = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "exec usp_doLoadJobCardReport2 '$dateFrom','$dateTo','$CustomerCode','$JobStatus','$JobType','$userId',$PerPage,'$CurrentPage'";


        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());


        $NUmberOfRecord = $res[1][0]['NUmberOfRecord'];
        $pages_count = ceil($NUmberOfRecord / $PerPage);
        $last_page  = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y'){
            $from = (($CurrentPage * $PerPage) + 1) - $PerPage;
            $to = ($CurrentPage * $PerPage);
        }
        $paginationData [] = [
            'current_page' => $CurrentPage,
            'last_page' => $last_page,
            'total' => (int)$NUmberOfRecord,
            'from' => $from,
            'to' => $to,
        ];

        return response()->json([
            'data' => $res[0],
            'paginationData' => $paginationData
        ]);

    }
    public function getJobCSIData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $JobStatus = $request->JobStatus;
        $JobType = $request->JobType;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $roleId = Auth::user()->RoleId;
        $userId = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "exec usp_doLoadJobCardReport2 '$dateFrom','$dateTo','$CustomerCode','$JobStatus','$JobType','$userId',$PerPage,'$CurrentPage','Y'";
        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());


        $NUmberOfRecord = $res[1][0]['NUmberOfRecord'];
        $pages_count = ceil($NUmberOfRecord / $PerPage);
        $last_page  = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y'){
            $from = (($CurrentPage * $PerPage) + 1) - $PerPage;
            $to = ($CurrentPage * $PerPage);
        }
        $paginationData [] = [
            'current_page' => $CurrentPage,
            'last_page' => $last_page,
            'total' => (int)$NUmberOfRecord,
            'from' => $from,
            'to' => $to,
        ];

        return response()->json([
            'data' => $res[0],
            'paginationData' => $paginationData
        ]);

    }

    public function getBookingReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;

        if(empty($request->CustomerCode)){
            $CustomerCode = Auth::user()->UserId;
        }

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "exec usp_doLoadJobCardBookingReport '$dateFrom','$dateTo','$CustomerCode',$PerPage,'$CurrentPage'";

        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        $NUmberOfRecord = $res[1][0]['NUmberOfRecord'];

        $pages_count = ceil($NUmberOfRecord / $PerPage);
        $last_page  = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y'){
            $from = (($CurrentPage * $PerPage) + 1) - $PerPage;
            $to = ($CurrentPage * $PerPage);
        }
        $paginationData [] = [
            'current_page' => $CurrentPage,
            'last_page' => $last_page,
            'total' => (int)$NUmberOfRecord,
            'from' => $from,
            'to' => $to,
        ];

        return response()->json([
            'data' => $res[0],
            'paginationData' => $paginationData
        ]);
    }

}
