<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblJobStatus;
use App\Models\JobCard\TblJobType;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiveReportController extends Controller
{
    use CommonTrait;
    public function getJobReportSupportingData(){
        $user  = $this->loadCustomer();
        $allJobType  = TblJobType::select('JobTypeName as text','Id as value',)
            ->where('ParentId','=',0)
            ->where('Active','Y')->orderBy('ReportOrder','asc')->get();

        return response()->json([
            'user' => $user,
        ]);
    }

    public function getReceiveReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $chassisNo = $request->chassisNo;
        $reportType = $request->reportType;

        $roleId = Auth::user()->RoleId;

        if (empty($request->CustomerCode) && $roleId !== 'admin') {
            $CustomerCode = Auth::user()->UserId;
        }

        if ($Export == 'Y') {
            $current_page = '%';
        }
        $sql = "exec usp_doLoadLogisticsDocumentReport '$dateFrom', '$dateTo', '$CustomerCode', 
                    '$chassisNo','$reportType','$PerPage','$CurrentPage'";

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
        $last_page = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y') {
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
    public function getReceiveSummaryReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $chassisNo = $request->chassisNo;
        $reportType = $request->reportType;

        $roleId = Auth::user()->RoleId;

        if (empty($request->CustomerCode) && $roleId !== 'admin') {
            $CustomerCode = Auth::user()->UserId;
        }

        if ($Export == 'Y') {
            $current_page = '%';
        }
        $sql = "exec usp_ReceiveReportSummary '$dateFrom', '$dateTo', '$CustomerCode','$PerPage','$CurrentPage'";

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
        $last_page = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y') {
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
