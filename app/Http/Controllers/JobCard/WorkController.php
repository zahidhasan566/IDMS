<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblBaySetup;
use App\Models\JobCard\TblWorkSetup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkController extends Controller
{
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;
        $roleId = Auth::user()->RoleId;

        $tblWorkSetup = TblWorkSetup::select(
            'WorkCode',
            'WorkName',
            'WorkRate',
            'Comment',
            'Active',
        )
            ->where(function ($q) use ($search) {
                $q->where('WorkCode', 'like', '%' . $search . '%');
                $q->Orwhere('WorkName', 'like', '%' . $search . '%');
            });

        if($roleId !=='admin'){
            $tblWorkSetup->where('ServiceCenterCode', Auth::user()->UserId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $tblWorkSetup->get(),
            ]);
        } else {
            return $tblWorkSetup->paginate($take);
        }
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'workName' => 'required',
            'workRate' => 'required',
            'active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Store Bay
            $userId = Auth::user()->UserId;
            $workCode = TblWorkSetup::select('WorkCode')->where('ServiceCenterCode',$userId)->orderbyRaw('convert(int,WorkCode) desc')->first();
            $updatedWorkCode = intval($workCode->WorkCode) + 1;

            $work = new TblWorkSetup();
            $work->ServiceCenterCode = $userId;
            $work->WorkCode = $updatedWorkCode;
            $work->WorkName = $request->workName;
            $work->WorkRate = $request->workRate;
            $work->Comment = $request->comments;
            $work->Active = $request->active;
            $work->IUser = $userId;
            $work->IDate = Carbon::now();
            $work->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Work Added Successfully'
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

    public function existingWorkInfo($workCode){
        $userId = Auth::user()->UserId;
        $existingWorkInfo = TblWorkSetup::select('WorkCode','WorkName','WorkRate','Comment','Active')
            ->where('ServiceCenterCode',$userId)
            ->where('WorkCode',$workCode)
            ->first();
        return response()->json([
            'existingWorkInfo'=> $existingWorkInfo
        ]);
    }
    public function updateWork(Request $request){
        $validator = Validator::make($request->all(), [
            'workName' => 'required',
            'workRate' => 'required',
            'active' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            //Update Work
            $userId = Auth::user()->UserId;

            $work = TblWorkSetup::where('WorkCode',$request->workCode)->update([
                'WorkName'=>$request->workName,
                'WorkRate'=>$request->workRate,
                'Comment'=>$request->comments,
                'Active'=>$request->active,
                'EUser'=>$userId,
                'EDate'=>Carbon::now()
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Work Updated Successfully'
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
