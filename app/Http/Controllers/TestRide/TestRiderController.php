<?php

namespace App\Http\Controllers\TestRide;

use App\Http\Controllers\Controller;
use App\Models\TestRide\TestRideAgents;
use App\Models\TestRide\TestRiders;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestRiderController extends Controller
{
    use CommonTrait;
    use CodeGeneration;
    public function index( Request $request) {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $list = DB::table('TestRiders as tr')->select('tr.*', 'c.CustomerName')
            ->join('Customer as c','c.CustomerCode','=','tr.DealerId')
            ->where('tr.DealerId','=', $userId)
            ->orwhere('tr.CustomerName', 'like', '%' . $search . '%')
            ->orwhere('tr.CustomerAddress', 'like', '%' . $search . '%');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $list->get(),
            ]);
        } else {
            return $list->orderbydesc('RideId')->paginate($take);
        }
    }
    public function show($rideId){
        $agents = DB::table('TestRiders as t')
            ->select('t.*','tg.AgentId','tg.Name','tg.DealerId')
            ->join('TestRideAgents as tg','tg.AgentId','tg.AgentId')
            ->where('RideId',$rideId)
            ->first();

        return response()->json([
            'agents'=> $agents
        ]);
    }

    public function getAllAgents(){
        $test = $this->getAllTestRideAgents();
        return $test;
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'customerName' => 'required',
            'mobile' => 'required',
            'agents' => 'required',
            'address' => 'required',
            'agentType' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        $rideId=$this->generateTestRideId();
        try{
            $uniqueNumber = TestRiders::where('CustomerMobile',$request->mobile)->get();

            if (!empty($uniqueNumber)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Give a Unique Mobile Number'
                ],500);
            }
            $test = new TestRiders();
            $test->rideId =$rideId;
            $test->CustomerName = $request->customerName;
            $test->CustomerMobile = $request->mobile;
            $test->AgentId = $request->agents['AgentId'];
            $test->DealerId = $request->agents['DealerId'];
            $test->CustomerAddress = $request->address;
            $test->CustomerType = $request->agentType;
            $test->CreatedAt = Carbon::now();
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
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'customerName' => 'required',
            'mobile' => 'required',
            'agents' => 'required',
            'address' => 'required',
            'agentType' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        try{
            $rideExist = TestRideAgents::where('CustomerMobile',$request->Mobile)->exists();
            if ($rideExist){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Already Exist'
                ],500);
            }
            $test = TestRiders::where('RideId',$request->rideId)->first();
            $test->CustomerName = $request->customerName;
            $test->CustomerMobile = $request->mobile;
            $test->AgentId = $request->agents[0]['AgentId'];
            $test->DealerId = $request->agents[0]['DealerId'];
            $test->CustomerAddress = $request->address;
            $test->CustomerType = $request->agentType;
            $test->CreatedAt = Carbon::now();
            $test->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Test Ride Update Successfully'
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
