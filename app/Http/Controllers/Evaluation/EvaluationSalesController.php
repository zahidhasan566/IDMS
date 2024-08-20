<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Evaluation\ServiceEvaluationMaster;
use App\Models\Evaluation\ServiceEvalutionDetails;
use App\Models\Evaluation\ServiceEvalutionManpower;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationSalesController extends Controller
{
    use CommonTrait;
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $serviceEvaluationMaster = ServiceEvaluationMaster::select(
            'ServiceEvalutionMaster.EvalutionId',
            'Customer.CustomerName',
            'District.DistrictName',
            'ServiceEvalutionMaster.EvalutedBy',
            DB::raw('convert(date,ServiceEvalutionMaster.EvalutedDate) as EvalutedDate'),
            DB::raw('convert(date,ServiceEvalutionMaster.OpeningDate) as OpeningDate'),
        )
            ->leftjoin('Customer','Customer.CustomerCode','ServiceEvalutionMaster.CustomerCode')
            ->leftjoin('District','District.DistrictCode','ServiceEvalutionMaster.DistrictCode')
            ->where('ServiceEvalutionMaster.EvalutionType', '=', 'Sales')
            ->orderBy('ServiceEvalutionMaster.EvalutionId', 'desc');


        if ($request->type === 'export') {
            return response()->json([
                'data' => $serviceEvaluationMaster->get(),
            ]);
        } else {
            return $serviceEvaluationMaster->paginate($take);
        }
    }
    public function editSalesDetails($evaluationId){
        $list = DB::table('ServiceEvalutionMaster as m')
            ->select( 'h.ServiceSubHeadID','s.ServiceHead','h.SeriveSubHead','s.ServiceHeadID','h.ServiceSubHeadID','d.Target','d.Weight','d.Actual','d.Score','d.observations')
//            ->select('m.CustomerCode','m.EvalutedBy','s.ServiceHeadID','d.*')
            ->leftJoin('ServiceEvalutionDetails as d','d.EvalutionId','=','m.EvalutionId')
            ->leftJoin('ServiceEvalutionSubHead as h','h.ServiceSubHeadID ','=','d.ServiceSubHeadID ')
            ->leftJoin('ServiceEvalutionHead as s','s.ServiceHeadID','=','h.ServiceHeadID ')
            ->where('d.EvalutionId','=',$evaluationId)
           ->get();

        return $list;
    }
      public function salesDetails( Request $request){
          $evaluationId= $request->evaluationId;
          $sales ='sales';
//          if (!empty($evaluationId)){
//              $list = $this->editSalesDetails($evaluationId);
//          }else{
//
//          }
          $list = DB::table("ServiceEvalutionHead as s")
              ->select( 's.ServiceHeadID','h.ServiceSubHeadID','s.ServiceHead','h.SeriveSubHead','h.Target','h.Weight')
              ->join('ServiceEvalutionSubHead as h','h.ServiceHeadId','=','s.ServiceHeadId')
              ->where('h.HeadType','=',$sales)
              ->where('h.active','=',1)
              ->where('s.active','=',1)
              ->orderBy('s.ServiceHead','ASC')->get();

         $headerList =DB::table('ServiceEvalutionHead')->select('ServiceHeadID','ServiceHead') ->where('HeadType','=',$sales)->get();
          return response()->json([
              'data' => $list,
              'headerData' => $headerList
          ]);
      }

      public function store(Request $request){
          $browser = get_browser();
          $sales ='sales';
          $evaluation =$request->evaluations;
          $evaluationBy =$request->allData['evaluationBy'];
          $openDate =$request->allData['openDate'];
          $evaluationDate =$request->allData['evaluationDate'];
          $dealer =$request->dealer['CustomerCode'];
          try {

              DB::beginTransaction();
              $evaluationMaster = new ServiceEvaluationMaster();
              $evaluationMaster->CustomerCode = $dealer;
              $evaluationMaster->EvalutedBy = $evaluationBy;
              $evaluationMaster->EvalutedDate = $evaluationDate;
              $evaluationMaster->OpeningDate = $openDate;
              $evaluationMaster->EvalutionType = $sales;
              $evaluationMaster->EntryBy = Auth::user()->UserId;
              $evaluationMaster->EntryDate = Carbon::now();
              $evaluationMaster->IpAddress = \request()->ip();
              $evaluationMaster->DiviceState = $browser->browser;
              if ($evaluationMaster->save()){
                  $manpower = new ServiceEvalutionManpower();
                  $manpower->EvalutionId =$evaluationMaster->EvalutionId;
                  $manpower->save();
                  foreach ($evaluation as $key){
                      $details = new ServiceEvalutionDetails();
                      $details->EvalutionId = $evaluationMaster->EvalutionId;
                      $details->ServiceSubHeadID = $key['ServiceSubHeadID'];
                      $details->Target =$key['Target'];
                      $details->Weight =$key['Weight'] ;
                      $details->actual =$key['actual'] ;
                      $details->score =round(($key['actual']/$key['Target'])*($key['Weight']),2);
                      $details->observations =$key['observations'];
                      $details->save();
                  }
                  DB::commit();
                  return response()->json([
                      'status' => 'success',
                      'message'=>'Successfully Stored'
                  ]);
              }else{
                  return response()->json([
                      'status' => 'error',
                      'message'=>'Upload Problem'
                  ]);
              }

          }catch (\Exception $e) {
              DB::rollBack();
              return response()->json([
                  'status' => 'error',
                  'message' => 'Failed to update evaluation'
              ], 500);
          }



      }

    public function getAllDealer(){
        $dealer = $this->loadCustomer();
        return response()->json([
            'dealer' => $dealer
        ]);
    }
    public function getUserRegion(Request $request){
        $dealer =$request->dealerCode;
        $district = DB::select("exec usp_doLoadUserWiseRegionList '$dealer'");
        return response()->json([
            'data' => $district
        ]);
    }
}
