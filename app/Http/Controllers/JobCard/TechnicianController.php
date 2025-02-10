<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblBaySetup;
use App\Models\JobCard\TblJobStatus;
use App\Models\JobCard\TblJobType;
use App\Models\JobCard\TblTechnicianSetup;
use App\Models\JobCard\TblTechnicianTrainingDetails;
use App\Models\JobCard\tblTechnicianTrainingList;
use App\Models\JobCard\TblWorkSetup;
use App\Models\JobCard\TechnicianDesignationList;
use App\Models\JobCard\TechnicianReasonOfResign;
use App\Models\JobCard\Territory;
use App\Models\User;
use App\Services\ImageBase64Service;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TechnicianController extends Controller
{
    use CommonTrait,CodeGeneration;
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;
        $userId  = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $tblTechnicianSetup = TblTechnicianSetup::select(
            'TblTechnicianSetup.Id',
            'TblTechnicianSetup.TechnicianCode',
            'TblTechnicianSetup.ServiceCenterCode',
            'TblTechnicianSetup.TechnicianEmpCode AS Employee_Code',
            'TblTechnicianSetup.TechnicianName',
            'TblTechnicianSetup.ContactNo AS Mobile',
            DB::raw('convert(date,TblTechnicianSetup.JoiningDate) as JoiningDate'),
            'TblTechnicianSetup.Address',
            'TblTechnicianSetup.EducationalQualification AS Educational_Qualification',
            'TblTechnicianSetup.Comment',
            'TblTechnicianSetup.ResignationDate',
            'TblTechnicianSetup.Designation',
            'TblTechnicianSetup.Active',
            'tblBaySetup.BayName'
        )
            ->leftjoin('tblBaySetup',function ($q) use($userId) {
                $q->on('tblBaySetup.BayCode','TblTechnicianSetup.DefaultBay');
                $q->where('tblBaySetup.ServiceCenterCode',$userId);
            })
            ->where(function ($q) use ($search) {
                $q->where('TblTechnicianSetup.TechnicianCode', 'like', '%' . $search . '%');
                $q->Orwhere('TblTechnicianSetup.TechnicianName', 'like', '%' . $search . '%');
            })
            ->orderBy('TblTechnicianSetup.Id','desc');

        if($roleId !=='admin'){
            $tblTechnicianSetup->where('TblTechnicianSetup.ServiceCenterCode', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $tblTechnicianSetup->get(),
            ]);
        } else {
            return $tblTechnicianSetup->paginate($take);
        }
    }
    public function getSupportingData(){
        $userId  = Auth::user()->UserId;
        $designationList = TechnicianDesignationList::all();
        $territoryList = Territory::select('Territory.TTYCode','Territory.TTYName')->where('Business','C')->where('DepotCode','H')->get();


        return response()->json([
            //'allBay' => $allBay,
            'customers' => $this->loadCustomer(),
            'designationList' => $designationList,
            'territoryList' => $territoryList,
        ]);
    }
    public function getBaySupportingData($serviceCenterCode){
        $allBay = TblBaySetup::select('BayCode','BayName')->where('ServiceCenterCode',$serviceCenterCode)->get();
        return response()->json([
            'allBay' => $allBay,
        ]);
    }
    public function getTrainingSupportingData($technicianCode){
        $userId  = Auth::user()->UserId;
        $trainingList  = TblTechnicianTrainingList::where('Active','Y')->get();
        $existingTrainingList  = TblTechnicianTrainingDetails::
                 select(
                     'TblTechnicianTrainingDetails.*',
                     'tblTechnicianTrainingList.TrainingName'
                    )
                ->join('tblTechnicianTrainingList','tblTechnicianTrainingList.Id','TblTechnicianTrainingDetails.TrainingId')
                ->where('TechnicianCode',$technicianCode)
                ->get();

        return response()->json([
            'trainingList' => $trainingList,
            'existingTrainingList' => $existingTrainingList,
        ]);
    }
    public function getResignSupportingData(){
        $reasonOfResign = TechnicianReasonOfResign::all();
        return response()->json([
            'reasonOfResignList' => $reasonOfResign,
        ]);
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'technicianName' => 'required',
            'active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Store Technician
            $userId = Auth::user()->UserId;

            //GENERATE NEW TECHNICIAN
            $technicianCode = $this->generatetechnicianNo();

            $technician = new TblTechnicianSetup();
            $technician->ServiceCenterCode = $request->dealerCode;
            $technician->TechnicianCode = $technicianCode;
            $technician->Territory = $request->territory? $request->territory['TTYCode']:'';
            $technician->TechnicianPhoto = $request->technicianPhoto? ImageBase64Service::imageResizeUpload($request->technicianPhoto, 'Technician', public_path('uploads/technicianPhoto/')):'';
            $technician->NetworkCategory = $request->networkCategory;
            $technician->DealerCategory = $request->dealerCategory;
            $technician->TechnicianEmpCode = $request->staffId?$request->staffId:null ;
            $technician->TechnicianName = $request->technicianName;
            $technician->ContactNo = $request->contactNo;
            $technician->JoiningDate = $request->joiningDate;
            $technician->Gender = $request->gender;
            $technician->DateOfBirth = $request->birthDate;
            $technician->Address = $request->address;
            $technician->EducationalQualification = $request->educationalQualification;
            $technician->Experience = $request->experience;
            $technician->Comment = $request->comment;
            $technician->Designation = $request->designation;
            $technician->Active = $request->active;
            $technician->IUser = $userId;
            $technician->IDate = Carbon::now();
            $technician->DefaultBay = $request->defaultBay? $request->defaultBay['BayCode'] :'';
            $technician->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Technician Added Successfully'
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

    public function existingTechnicianInfo($technicianCode){
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;
        $existingTechnicianInfo = TblTechnicianSetup::select(
            'TblTechnicianSetup.ServiceCenterCode',
            'TblTechnicianSetup.TechnicianCode',
            'TblTechnicianSetup.Territory',
            'Territory.TTYName',
            'TblTechnicianSetup.TechnicianPhoto',
            'TblTechnicianSetup.NetworkCategory',
            'TblTechnicianSetup.DealerCategory',
            'TblTechnicianSetup.TechnicianEmpCode',
            'TblTechnicianSetup.TechnicianName',
            'TblTechnicianSetup.ContactNo',
            DB::raw('convert(date,TblTechnicianSetup.JoiningDate) as JoiningDate'),
            'TblTechnicianSetup.Gender',
            DB::raw('convert(date,TblTechnicianSetup.DateOfBirth) as DateOfBirth'),
            'TblTechnicianSetup.Address',
            'TblTechnicianSetup.EducationalQualification',
            'TblTechnicianSetup.Experience',
            'TblTechnicianSetup.Training',
            'TblTechnicianSetup.Comment',
            'TblTechnicianSetup.Designation',
            'TblTechnicianSetup.Active',
            'TblTechnicianSetup.DefaultBay',
            'tblBaySetup.BayName'
        )
            ->leftjoin('tblBaySetup',function ($q) use($userId) {
                $q->on('tblBaySetup.BayCode','TblTechnicianSetup.DefaultBay');
                $q->on('tblBaySetup.ServiceCenterCode','TblTechnicianSetup.ServiceCenterCode');
            })
            ->leftjoin('Territory','Territory.TTYCode','TblTechnicianSetup.Territory')
          ->where('TblTechnicianSetup.TechnicianCode', $technicianCode)
          ->first();
        if($roleId !=='admin'){
            $existingTechnicianInfo->where('TblTechnicianSetup.ServiceCenterCode', $userId);
        }

        return response()->json([
            'existingTechnicianInfo'=> $existingTechnicianInfo
        ]);
    }
    public function updateTechnician(Request $request){
        $validator = Validator::make($request->all(), [
            'technicianName' => 'required',
            'active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Update Technician
            $defaultBay='';
            $userId = Auth::user()->UserId;
            if($request->technicianPhoto){
                $existingPic =  TblTechnicianSetup::where('TechnicianCode',$request->technicianCode)->where('ServiceCenterCode',$request->dealerCode)->first();
                if( !empty($existingPic->TechnicianPhoto) && file_exists(public_path('uploads/technicianPhoto/'.$existingPic->TechnicianPhoto))){
                    unlink(public_path('uploads/technicianPhoto/'.$existingPic->TechnicianPhoto));
                }
            }

            TblTechnicianSetup::where('TechnicianCode',$request->technicianCode)->where('ServiceCenterCode',$request->dealerCode)->update([
                'ServiceCenterCode'=>$request->dealerCode,
                'Territory'=> $request->territory? $request->territory['TTYCode']:'',
                'TechnicianPhoto'=>$request->technicianPhoto? ImageBase64Service::imageResizeUpload($request->technicianPhoto, 'Technician', public_path('uploads/technicianPhoto/')):'',
                'NetworkCategory'=>$request->networkCategory,
                'DealerCategory'=>$request->dealerCategory,
                'TechnicianEmpCode'=>$request->staffId,
                'TechnicianName'=>$request->technicianName,
                'ContactNo'=>$request->contactNo,
                'JoiningDate'=>$request->joiningDate,
                'Gender'=>$request->gender,
                'DateOfBirth'=>$request->birthDate,
                'Address'=>$request->address,
                'EducationalQualification'=>$request->educationalQualification,
                'Experience'=>$request->experience,
                'Training'=>$request->training,
                'Comment'=>$request->comment,
                'Designation'=>$request->designation,
                'Active'=>$request->active,
                'DefaultBay'=>isset($request->defaultBay['BayCode'])? $request->defaultBay['BayCode'] : $request->defaultBay[0]['BayCode'],
                'EUser'=>$userId,
                'EDate'=>Carbon::now()
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Technician Updated Successfully'
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
    public function getReportSupportingData(){
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
    public function getReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;

        $roleId = Auth::user()->RoleId;

        if(empty($request->CustomerCode) && $roleId !=='admin'){
            $CustomerCode = Auth::user()->UserId;
        }
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "exec usp_doLoadTechnicianWiseReport '$dateFrom','$dateTo','$CustomerCode',$PerPage,'$CurrentPage'";

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
    public function resignTechnician(Request $request){
        $validator = Validator::make($request->all(), [
            'technicianCode' => 'required',
            'resignationDate' => 'required',
            'reasonOfResignation' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        try{
            TblTechnicianSetup::where('TechnicianCode',$request->technicianCode)->update([
                'Active'=> 'N',
                'ResignationDate'=> $request->resignationDate,
                'ReasonOfResign'=> $request->reasonOfResignation ? $request->reasonOfResignation['ReasonOfResign']:'',
                'EUser'=> Auth::user()->UserId,
                'EDate'=> Carbon::now(),
            ]);
            return response()->json([
                'status' => 'Success',
                'message' => 'Technician Resigned Successfully'
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
    public function storeTechnicianTraining(Request $request){
        $validator = Validator::make($request->all(), [
            'trainingId' => 'required',
            'trainingDate' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        try{
            $existingTraining = TblTechnicianTrainingDetails::where('TechnicianCode',$request->technicianCode)
                ->where('TrainingId',$request->trainingId)->first();
            if($existingTraining){
                TblTechnicianTrainingDetails::where('TechnicianCode',$request->technicianCode)
                    ->where('TrainingId',$request->trainingId)->update([
                        'TrainingDate'=>$request->trainingDate,
                        'Mark'=>$request->marks
                    ]);
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Training Updated Successfully',
                    'request' => $request->all()
                ],200);
            }
            else{
                TblTechnicianTrainingDetails::firstOrCreate([
                    'TechnicianCode'=>$request->technicianCode,
                    'TrainingId'=>$request->trainingId,
                    'TrainingDate'=>$request->trainingDate,
                    'Mark'=>$request->marks,
                    'IpAddress'=>$request->ip(),
                    'IUse'=> Auth::user()->UserId,
                    'IDate'=> Carbon::now()
                ]);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Training Added Successfully',
                    'request' => $request->all()
                ],200);
            }



        }
        catch (\Exception $exception) {
//            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
}
