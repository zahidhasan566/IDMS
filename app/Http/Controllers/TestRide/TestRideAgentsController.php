<?php

namespace App\Http\Controllers\TestRide;

use App\Http\Controllers\Controller;
use App\Models\TestRide\TestRideAgents;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TestRideAgentsController extends Controller
{
    use CommonTrait;
    public function index( Request $request) {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $list = TestRideAgents::where('DealerId','=', $userId)
            ->orwhere('Name', 'like', '%' . $search . '%')
            ->orwhere('DealerPoint', 'like', '%' . $search . '%')
            ->orwhere('YRCRegion', 'like', '%' . $search . '%');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $list->get(),
            ]);
        } else {
            return $list->orderbydesc('AgentId')->paginate($take);
        }
    }
    public function getAllList(){
      $test = $this->getAllListForRide();
      return $test;
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'agentName' => 'required',
            'mobile' => 'required',
            'dealerPoint' => 'required',
            'presentBike' => 'required',
            'yrcRegion' => 'required',
            'chassisNo' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }


        try{
            $uniqueNumber = TestRideAgents::where('Mobile',$request->mobile)->get();

            if (!empty($uniqueNumber)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Give a Unique Mobile Number'
                ],500);
            }
            $test = new TestRideAgents();
            $test->Name = $request->agentName;
            $test->Mobile = $request->mobile;
            $test->DealerPoint = $request->dealerPoint['CustomerName'];
            $test->DealerId = $request->dealerPoint['CustomerCode'];
            $test->PresentBike = $request->presentBike['PresentBike'];
            $test->YRCRegion = $request->yrcRegion['YRCRegion'];
            $test->ChassisNo = $request->chassisNo;
            $test->Profession = $request->profession;

            $test->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Test Ride Agent Added Successfully'
            ],200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function show($agentId){
        $agents = DB::table('TestRideAgents as t')
            ->select('t.*','c.CustomerCode','c.CustomerName',DB::raw("CONCAT(c.CustomerCode,': ', c.CustomerName )as CustomerInfo"))
            ->join('Customer as c','c.CustomerCode','t.DealerId')
            ->where('AgentId',$agentId)
            ->first();

        return response()->json([
            'agents'=> $agents
        ]);
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'agentName'     => 'required',
            //'Mobile'        => 'required|unique:TestRideAgents,AgentId,null,' . $request->agentId,
            'Mobile'        => 'required',
            'dealerPoint'   => 'required',
            'presentBike'   => 'required',
            'yrcRegion'     => 'required',
            'chassisNo'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        try{
//            $agentExist = TestRideAgents::where('Mobile',$request->Mobile)->where('AgentId',$request->agentId)->exists();
            $test = TestRideAgents::where('AgentId',$request->agentId)->first();
                $agentExist = TestRideAgents::where('Mobile',$request->Mobile)->exists();
                if ($agentExist){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Already Exist'
                    ],500);
                }else{
                    $test->Name = $request->agentName;
                    $test->DealerPoint = $request->dealerPoint[0]['CustomerName'];
                    $test->DealerId = $request->dealerPoint[0]['CustomerCode'];
                    $test->PresentBike = $request->presentBike[0]['PresentBike'];
                    $test->YRCRegion = $request->yrcRegion[0]['YRCRegion'];
                    $test->ChassisNo = $request->chassisNo;
                    $test->Profession = $request->profession;
                    $test->Mobile = $request->Mobile;
                    $test->save();

                    return response()->json([
                        'status' => 'Success',
                        'message' => 'Test Ride Agent Updated Successfully'
                    ],200);
                }
//            }
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
}
