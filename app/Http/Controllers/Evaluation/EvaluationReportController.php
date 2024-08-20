<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Evaluation\ServiceEvaluationMaster;
use App\Models\Logistics\DealerDocument;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationReportController extends Controller
{
    use CommonTrait;
    public function index(Request $request){
        $CurrentPage = isset($request->pagination['current_page']) ? $request->pagination['current_page'] : 1;
        $PerPage = $request->take;
        $Export = $request->Export;
        $CustomerCode = $request->customerCode;
        $DateFrom=$request->dateFrom;
        $DateTo =$request->dateTo;
        $ReportType =$request->reportType;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportServiceEvalution  '$DateFrom', '$DateTo', '$CustomerCode','$ReportType' ";
        $list = DB::select($sql);
        return response()->json([
            'data'=>$list
        ]);
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);

    }
    public function detailsReport( Request $request){

        $evaluationId= $request->evaluationId;
        $conn = DB::connection('sqlsrvread');
        $sql = "exec usp_reportServiceEvalutionDetails '$evaluationId'";
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();

        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        if (empty($res[2])){
            $sales ='sales';
        }else{
            $sales ='service4p';
        }
        $headerList =DB::table('ServiceEvalutionHead')
            ->select('ServiceHeadID','ServiceHead')
            ->where('HeadType','=',$sales)
            ->where('Active','=',1)
            ->orderBy('OrderSl','ASC')
            ->get();

        return response()->json([
           'dealer'=>$res[0],
           'data'=>$res[1],
           'manpower'=>$res[2],
           'headerData'=>$headerList,
        ]);

//        $sales ='sales';
//        $list = DB::table('ServiceEvalutionMaster as m')
//            ->select('m.*',
//                DB::raw("CONCAT(m.CustomerCode,': ',c.CustomerName) as Dealer,
//                Convert(date,m.EvalutedDate) as EvaluationDate,Convert(date,m.OpeningDate) as OpenDate, CONCAT(m.DistrictCode,': ',ds.DistrictName) as District"),
//                's.ServiceHead', 'h.SeriveSubHead','s.ServiceHeadID','h.ServiceSubHeadID',
//                'd.Target','d.Weight','d.Actual','d.Score','d.observations')
//
//            ->leftJoin('ServiceEvalutionDetails as d','d.EvalutionId','=','m.EvalutionId')
//            ->leftJoin('ServiceEvalutionSubHead as h','h.ServiceSubHeadID ','=','d.ServiceSubHeadID ')
//            ->leftJoin('ServiceEvalutionHead as s','s.ServiceHeadID','=','h.ServiceHeadID ')
//            ->join('Customer as c','c.CustomerCode','=','m.CustomerCode ')
//            ->join('District as ds','ds.DistrictCode','=','m.DistrictCode ')
//            ->where('d.EvalutionId','=',$evaluationId)
//            ->get();
//
//        $headerList =DB::table('ServiceEvalutionHead')->select('ServiceHeadID','ServiceHead') ->where('HeadType','=',$sales)->get();
//        return response()->json([
//            'data' => $list,
//            'headerData' => $headerList
//        ]);
    }





}
