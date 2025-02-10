<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\Evaluation\EvaluationDesignation;
use App\Models\Evaluation\EvaluationMethod;
use App\Models\Evaluation\ServiceEvaluationDetails;
use App\Models\Evaluation\ServiceEvaluationHead;
use App\Models\Evaluation\ServiceEvaluationMaster;
use App\Models\Evaluation\ServiceEvalutionCheckPointDetails;
use App\Models\Evaluation\ServiceEvalutionManpower;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use mysql_xdevapi\Exception;

class EvaluationForService4PController extends Controller
{
    use CommonTrait;

    public function index(Request $request)
    {
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
            'ServiceEvalutionMaster.ServiceAreaVolumn',
            'ServiceEvalutionMaster.SericeOpeningCloseingTime',
        )
            ->leftjoin('Customer', 'Customer.CustomerCode', 'ServiceEvalutionMaster.CustomerCode')
            ->leftjoin('District', 'District.DistrictCode', 'ServiceEvalutionMaster.DistrictCode')
            ->where('ServiceEvalutionMaster.EvalutionType', '=', 'Service')
            ->orderBy('ServiceEvalutionMaster.EvalutionId', 'desc');


        if ($request->type === 'export') {
            return response()->json([
                'data' => $serviceEvaluationMaster->get(),
            ]);
        } else {
            return $serviceEvaluationMaster->paginate($take);
        }
    }

    public function getSupportingData()
    {
        $dealer = $this->loadCustomer();
        $evaluationDesignation = EvaluationDesignation::all();
        $serviceEvaluationHeadTitle = ServiceEvaluationHead::select('ServiceHeadID', 'ServiceHead')->where('HeadType', 'Service4p')->get();

        $evaluationMethod = EvaluationMethod::where('EvalutionMethodStatus', 'Y')->get();
        $conn = DB::connection('sqlsrv');

        $sql = "exec usp_doLoadEvalutionHead 'Service4p'";
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        return response()->json([
            'dealer' => $dealer,
            'evaluationDesignation' => $evaluationDesignation,
            'evaluationMethod' => $evaluationMethod,
            'evaluationHead' => $res[0],
            'serviceEvaluationHeadTitle' => $serviceEvaluationHeadTitle,
        ]);
    }

    public function store4p(Request $request)
    {
        try {
            //dd($request);
            DB::beginTransaction();
            //Browser Info
            $agent = new Agent();
            $browser = $agent->browser();
            $version = $agent->version($browser);
            $browser_version = $browser . ' ' . $version;
            $platform = $agent->platform();
            $version = $agent->version($platform);
            $platform_version = $platform . ' ' . $version;
            $device_and_browser_info = $browser_version . ' ' . $platform_version;

            //Evaluation Master
            $evaluationMaster = new ServiceEvaluationMaster();
            $evaluationMaster->CustomerCode = $request->dealer['CustomerCode'];
            //$evaluationMaster->DistrictCode = $request->district;
            $evaluationMaster->EvalutedBy = $request->evaluationBy;
            $evaluationMaster->EvalutedDate = $request->evaluationDate;
            $evaluationMaster->OpeningDate = $request->openDate;
            $evaluationMaster->EvalutionType = 'Service4p';
            $evaluationMaster->ServiceAreaVolumn = $request->serviceAreaVolume;
            $evaluationMaster->SericeOpeningCloseingTime = $request->serviceOpeningClosingTime;
            $evaluationMaster->ServiceBay = $request->bay['bayCode'];
            $evaluationMaster->EntryBy = Auth::user()->UserId;
            $evaluationMaster->EntryDate = Carbon::now();
            $evaluationMaster->IpAddress = $request->ip();
            $evaluationMaster->DiviceState = '';
            $evaluationMaster->save();


            //MANPOWER DATA
            if ($request->allEvaluationDesignation) {
                $allEvaluationDesignation = $request->allEvaluationDesignation;
                foreach ($allEvaluationDesignation as $singleEvaluationDesignation) {
                    $manpower = new ServiceEvalutionManpower();
                    $manpower->EvalutionId = $evaluationMaster->EvalutionId;
                    $manpower->DesignationId = $singleEvaluationDesignation['DesignationId'];
                    $manpower->ManpowerCount = $singleEvaluationDesignation['DesignationDropDown'];
                    $manpower->save();
                }
            }

            //EVALUATION DETAILS
            if ($request->allEvaluationHeadGroup) {
                $allEvaluationHeadGroupn = $request->allEvaluationHeadGroup;
                foreach ($allEvaluationHeadGroupn as $singleEvaluationHeadGroup) {
                    foreach ($singleEvaluationHeadGroup['details'] as $singleDetail){
                        if(!empty($singleDetail['inputValue'])){
                            //dd($singleDetail['selectedItem']);
                            $serviceEvaluationDetails= new ServiceEvaluationDetails();
                            $serviceEvaluationDetails->EvalutionId = $evaluationMaster->EvalutionId;
                            $serviceEvaluationDetails->ServiceSubHeadID = $singleDetail['ServiceSubHeadID'];
                            $serviceEvaluationDetails->ServiceSubSubHeadID = $singleDetail['ServiceSubSubHeadID'];
                            $serviceEvaluationDetails->RequirmentId = $singleDetail['RequirmentId'];
                            $serviceEvaluationDetails->EvalutionMethodId =!empty($singleDetail['selectedItem'])? $singleDetail['selectedItem']:'';
                            $q1_CheckPointWeight = 0;
                            $q2_CheckPointWeight = 0;
                            $q3_CheckPointWeight = 0;
                            if(!empty($singleDetail['Q1_CheckPointWeight'])){ $q1_CheckPointWeight = floatval($singleDetail['Q1_CheckPointWeight']); }
                            if(!empty($singleDetail['Q2_CheckPointWeight'])){ $q2_CheckPointWeight = floatval($singleDetail['Q2_CheckPointWeight']); }
                            if(!empty($singleDetail['Q3_CheckPointWeight'])){ $q3_CheckPointWeight = floatval($singleDetail['Q3_CheckPointWeight']); }
                            $serviceEvaluationDetails->Target = max($q1_CheckPointWeight,$q2_CheckPointWeight,$q3_CheckPointWeight);
                            $serviceEvaluationDetails->Actual = $singleDetail['inputValue'];
                            $serviceEvaluationDetails->Weight = max($q1_CheckPointWeight,$q2_CheckPointWeight,$q3_CheckPointWeight);
                            $serviceEvaluationDetails->Score = $singleDetail['inputValue'];
                            //$serviceEvaluationDetails->Observations = $singleDetail->EvalutionId;
                            $serviceEvaluationDetails->save();
                        }
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'Success',
                'message' => ' First Part Saved Successfully',
                'evaluationId' => $evaluationMaster->EvalutionId
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function getSupportingDataPart2($evaluationId){

        $part2Requirements=  ServiceEvaluationDetails::select(
                                'EvalutionRequirmentMaster.RequirmentId',
                                'EvalutionRequirmentMaster.RequirmentName',
                                'EvalutionRequirmentMaster.RequirmentDescription',
                                )
                            ->join('EvalutionRequirmentMaster','EvalutionRequirmentMaster.RequirmentId','ServiceEvalutionDetails.RequirmentId')
                            ->where('ServiceEvalutionDetails.EvalutionId',$evaluationId)
                            ->where('ServiceEvalutionDetails.Target','!=',0)
                            ->orderBy('EvalutionRequirmentMaster.RequirmentId','asc')
                            ->get();

        return response()->json([
            'part2Requirements' => $part2Requirements
        ], 200);

    }

    public function store4pPart2(Request $request){
        $evaluationId = $request->evaluationId;
        $allServiceEvaluationProblemDetails = $request->allServiceEvaluationProblemDetails;
        try{
            foreach ($allServiceEvaluationProblemDetails as $singleServiceProblem){

                if(!empty($evaluationId) && !empty($singleServiceProblem['RequirmentId']) && !empty($singleServiceProblem['reason'])
                && !empty($singleServiceProblem['whatHappend']) && !empty($singleServiceProblem['whatToDo']) && !empty($singleServiceProblem['personIncharge'])){

                    $serviceEvaluationCheckPointDetails = new ServiceEvalutionCheckPointDetails();
                    $serviceEvaluationCheckPointDetails->EvalutionId =$evaluationId;
                    $serviceEvaluationCheckPointDetails->RequirmentId =$singleServiceProblem['RequirmentId'];
                    $serviceEvaluationCheckPointDetails->Reason =$singleServiceProblem['reason'];
                    $serviceEvaluationCheckPointDetails->WhatHappen =$singleServiceProblem['whatHappend'];
                    $serviceEvaluationCheckPointDetails->WhatToDo =$singleServiceProblem['whatToDo'];
                    $serviceEvaluationCheckPointDetails->Deadline =$singleServiceProblem['deadline'];
                    $serviceEvaluationCheckPointDetails->PersonIncharge =$singleServiceProblem['personIncharge'];
                    $serviceEvaluationCheckPointDetails->save();

                }
            }
            return response()->json([
                'status' => 'Success',
                'message' => ' Second Part Saved Successfully',
            ], 200);

        }
        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

}
