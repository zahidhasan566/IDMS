<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblBaySetup;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BayController extends Controller
{
    use CommonTrait;
    public function index(Request $request){
        $roleId = Auth::user()->RoleId;
        $take = $request->take;
        $search = $request->search;

        $tblBaySetup = TblBaySetup::select(
            'BayCode',
            'BayName',
            'ServiceCenterCode',
            'Comment',
            'Active',
            'IDate'
        )
        ->where(function ($q) use ($search) {
            $q->where('BayCode', 'like', '%' . $search . '%');
            $q->Orwhere('BayName', 'like', '%' . $search . '%');
        })
        ->orderBy('IDate','desc');
        if($roleId !=='admin'){
            $tblBaySetup->where('ServiceCenterCode', Auth::user()->UserId);
        }

        if ($request->type === 'export') {
            return response()->json([
                'data' => $tblBaySetup->get(),
            ]);
        } else {
            return $tblBaySetup->paginate($take);
        }
    }
    public function getSupportingData(){
        $roleId = Auth::user()->RoleId;
        return response()->json([
            'customers' => $this->loadCustomer(),
        ]);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'dealerCode' => 'required',
            'bayName' => 'required',
            'active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Store Bay
            $userId = Auth::user()->UserId;
            $roleId = Auth::user()->RoleId;
            if($roleId !=='admin'){
                $dealerCode = Auth::user()->UserId;
            }
            else{
                $dealerCode = $request->dealerCode;
            }

            $bayCode = TblBaySetup::select('BayCode')->where('ServiceCenterCode',$dealerCode)->orderbyRaw('convert(int,BayCode) desc')->first();
            if(!empty($bayCode->BayCode)){
                $updatedBayCode = intval($bayCode->BayCode) + 1;
            }
            else{
                $updatedBayCode =01;
            }



            $bay = new TblBaySetup();
            $bay->ServiceCenterCode = $request->dealerCode;
            $bay->BayCode = $updatedBayCode;
            $bay->BayName = $request->bayName;
            $bay->Comment = $request->comments;
            $bay->Active = $request->active;
            $bay->IUser = $userId;
            $bay->IDate = Carbon::now();
            $bay->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Bay Added Successfully'
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

    public function existingBayInfo($bayCode,$serviceCenterCode){
        $userId = Auth::user()->UserId;
        $existingBayInfo = TblBaySetup::select('TblBaySetup.BayCode','TblBaySetup.ServiceCenterCode','UserManager.UserName','TblBaySetup.BayName','TblBaySetup.Comment','TblBaySetup.Active')
            ->leftjoin('UserManager','UserManager.UserId','TblBaySetup.ServiceCenterCode')
            ->where('TblBaySetup.ServiceCenterCode',$serviceCenterCode)
            ->where('TblBaySetup.BayCode',$bayCode)
            ->first();

        return response()->json([
           'existingBayInfo'=> $existingBayInfo
        ]);
    }
    public function updateBay(Request $request){
        $validator = Validator::make($request->all(), [
            'dealerCode' => 'required',
            'bayName' => 'required',
            'active' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Update Bay
            $userId = Auth::user()->UserId;
            $bay = TblBaySetup::where('BayCode',$request->bayCode)->where('ServiceCenterCode',$request->dealerCode)->update([
                'ServiceCenterCode'=>$request->dealerCode,
                'BayName'=>$request->bayName,
                'Comment'=>$request->comments,
                'Active'=>$request->active,
                'EUser'=>$userId,
                'EDate'=>Carbon::now()
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Bay Updated Successfully'
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

}
