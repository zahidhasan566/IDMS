<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\FreeServiceSchedule;
use App\Models\JobCard\JobcardEstimation;
use App\Models\JobCard\JobcardEstimationDetails;
use App\Models\JobCard\TblJobCard;
use App\Models\JobCard\TblJobCardDetailSparepartWork;
use App\Models\JobCard\TblJobCardProblemDetails;
use App\Services\FileBase64Service;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobCardEstimationController extends Controller
{
    use CommonTrait,CodeGeneration;
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;
        $userId  = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $estimation = JobcardEstimation::select(
            'JobcardEstimation.EstimationNo',
            DB::raw('convert(date,JobcardEstimation.EstiamtionDate) as EstimationDate'),
            'JobcardEstimation.Chassisno',
            'JobcardEstimation.CustomerName',
            'JobcardEstimation.ContactNo',
            'JobcardEstimation.EngineNo',
            'JobcardEstimation.ModelName'
        )
            ->orderBy('JobcardEstimation.EstimationNo','desc');

        if($roleId !=='admin'){
            $estimation->where('JobcardEstimation.EntryBy', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $estimation->get(),
            ]);
        } else {
            return $estimation->paginate($take);
        }
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'jobCardNo' => 'required',
            'jobDate' => 'required',
            'chassisNo' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        try{
            //Store TblJobCard
            DB::beginTransaction();
            $userId  = Auth::user()->UserId;
            $jobCardNo =  DB::select("exec usp_doLoadJobCardNoNew '$userId' ");
            $jobCardNo =  $jobCardNo ? $jobCardNo[0]->JobCardNo:'';

            $estimation = new JobcardEstimation();
            $estimation->EstimationNo = $jobCardNo;
            $estimation->EstiamtionDate = $request->jobDate;
            $estimation->ChassisNo = $request->chassisNo ? $request->chassisNo['chassisno']:'';
            $estimation->CustomerName = $request->customerName;
            $estimation->ContactNo = $request->mobileNo;
            $estimation->EngineNo = $request->engineNo;
            $estimation->ModelName = $request->model;
            $estimation->EntryBy =  $userId;
            $estimation->save();


            //Estimation Details
            if(!empty($request->partsFields)){
                foreach ($request->partsFields as $singleParts){
                    if($singleParts['partsCode'] !==null){
                        if(isset($singleParts['partsCode']['ProductCode'])){
                            $itemCode = $singleParts['partsCode']['ProductCode'];
                        }
                        else{
                            $itemCode =  $singleParts['partsCode'][0]['ProductCode'];
                        }
                        if(!empty($itemCode)){
                            $estimationDetails = new JobcardEstimationDetails();
                            $estimationDetails->EstimationNo  = $jobCardNo;
                            $estimationDetails->ItemCode  =  $itemCode;
                            $estimationDetails->Quantity  = $singleParts['quantity'] ;
                            $estimationDetails->UnitPrice  =  $singleParts['unitPrice'];
                            $estimationDetails->TotalPrice  = $singleParts['totalPrice'];
                            $estimationDetails->ServiceCharge  = $singleParts['serviceCharge'];
                            $estimationDetails->Discount  = $singleParts['discount'];
                            $estimationDetails->save();
                        }
                    }

                }

            }
            Db::commit();

            return response()->json([
                'status' => 'Success',
                'message' => 'Job Card Estimation Added Successfully'
            ],200);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
    public function existingEstimation($estimationNo){

        $estimation = JobcardEstimation::with('JobcardEstimationDetails','JobcardEstimationDetails.Product')->where('EstimationNo',$estimationNo)->get();
        return response()->json([
            'data'=>$estimation
        ]);
    }

}
