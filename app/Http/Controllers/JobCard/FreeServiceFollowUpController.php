<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\FreeServiceSchedule;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FreeServiceFollowUpController extends Controller
{
    use CommonTrait;
  public function index(Request $request){


      $CurrentPage = isset($request->pagination['current_page']) ? $request->pagination['current_page'] : 1;
      $PerPage = $request->take;
      $Export = $request->Export;
      $CustomerCode = Auth::user()->UserId;
      $DateFrom=$request->DateFrom;
      $DateTo =$request->DateTo;
//      if (empty($DateFrom && $DateTo)){
//          $DateFrom =date('Y-m-d',strtotime( '+1 days' ));
//          $DateTo = date('Y-m-d', strtotime( '+2 days' ) ) ;
//      }

      if ($Export == 'Y'){
          $CurrentPage = '%';
      }
      $sql = " exec usp_reportEstimatedFreeServiceSchedule  '$DateFrom', '$DateTo', '$CustomerCode','','$CustomerCode','$PerPage','%','N' ";
      $list = DB::select($sql);
      return response()->json([
          'data'=>$list
      ]);
      return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);

  }
    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'scheduleId' => 'required',
            'remarks' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        try{
            $remark = FreeServiceSchedule::where('FreeSScheduleID',$request->scheduleId)->first();
            $remark->Remark = $request->remarks;
            $remark->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Follow Up Updated Successfully'
            ],200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
}
