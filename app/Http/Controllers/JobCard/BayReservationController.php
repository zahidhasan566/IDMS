<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\JobCard\OnlineReservation;
use App\Models\JobCard\OnlineReservationDetails;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\New_;

class BayReservationController extends Controller
{
    use CommonTrait;
    public function index(Request $request){
        $roleId = Auth::user()->RoleId;
        $take = $request->take;
        $search = $request->search;

        $reservation = DB::table('OnlineReservation as R')
            ->select('R.CustomerCode','C.CustomerName','R.CustomerEmail','R.MobileNO','R.NoOfBay',
                DB::raw("CASE WHEN R.Active ='Y' THEN 'Active' ELSE 'Inactive' END AS Active"))
            ->join('Customer as C','C.CustomerCode','=','R.CustomerCode')
            ->where(function ($q) use ($search) {
                $q->where('R.CustomerCode', 'like', '%' . $search . '%');
                $q->Orwhere('C.CustomerName', 'like', '%' . $search . '%');
            })
            ->orderBy('R.CustomerCode','ASC');

        if ($request->type === 'export') {
            return response()->json([
                'data' => $reservation->get(),
            ]);
        } else {
            return $reservation->paginate($take);
        }
    }
    public function getCustomerBay(Request $request,$customerCode){
        $bays = OnlineReservationDetails::select('BayName')->where('CustomerCode','=',$customerCode)->get();
            return response()->json([
                'data'=>$bays
            ]);

    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'allData' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        $reservations = $request->allData['BayName'];
        $customer = $request->allData['dealerCode'];
        $MobileNo = $request->allData['MobileNo'];
        $CustomerEmail = $request->allData['CustomerEmail'];
        $Active = $request->allData['Active'];
        try{
            $reservationExist = DB::table('OnlineReservationDetails')
                ->where('CustomerCode','=',$customer)->delete();
            $reservationExist = DB::table('OnlineReservation')
                ->where('CustomerCode','=',$customer)->delete();

                 DB::beginTransaction();
                 $reservation = new OnlineReservation();
                 $reservation->CustomerCode = $customer;
                 $reservation->MobileNO = $MobileNo;
                 $reservation->CustomerEmail = $CustomerEmail;
                 $reservation->NoOfBay = count($reservations);
                 $reservation->Active = $Active;

                 if ($reservation->save()){
                     foreach ($reservations as $item){
                         if (!empty($item)){
                             $bay = new OnlineReservationDetails();
                             $bay->CustomerCode = $customer;
                             $bay->BayName = $item['BayName'];
                             $bay->save();
                         }
                     }
                 }
                 DB::commit();
                 return response()->json([
                     'status' => 'success',
                     'message'=>'Bay Reservation Added Successfully'
                 ]);

        }
        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
    public function existingBayReserve($customerCode){
        $bayReserve = DB::table('OnlineReservation as OR')->select(
            'OR.CustomerCode','OR.CustomerEmail','OR.MobileNO','OR.NoOfBay','OR.Active','ORD.BayName')
            ->leftjoin('OnlineReservationDetails as ORD','OR.CustomerCode','ORD.CustomerCode')
            ->where('OR.CustomerCode',$customerCode)
            ->get();

        return response()->json([
            'data'=> $bayReserve
        ]);
    }
}
