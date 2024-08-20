<?php

namespace App\Http\Controllers;

use App\Models\ChallanMaster;
use App\Models\Transport;
use App\Models\TransportNotification\Notification;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\DeclareDeclare;

class NotificationController extends Controller
{

    use CommonTrait;
    public function index( Request $request) {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $customerList = DB::select(DB::raw("exec usp_reportCustomerList '$userId'"));


        foreach ($customerList as $keys){
            $customer[] = $keys->CustomerCode;
        }

        $list = DB::table('TransportNotification as n ')
            ->select('n.NotificationID', 'n.CustomerCode','c.CustomerName', 'n.CustomerMobile','t.TransportName', 'n.ChallanNo',
                'n.ContactNo', 'n.DriverName','n.MOTMNumber', 'n.DriverContactNo','n.DeliveryDate', 'n.DeliveryTime', 'n.EntryBy', 'n.EntryDATE')
            ->join('Customer as c','c.CustomerCode','=','n.CustomerCode')
            ->join('Transport as t','t.TransportID','=','n.TransportID')
            ->whereIn('c.CustomerCode',$customer)
            ->orwhere('c.CustomerName', 'like', '%' . $search . '%');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $list->get(),
            ]);
        } else {
            return $list->orderbydesc('n.NotificationID')->paginate($take);
        }
    }

    public function getTransportList(){
        $Transport =Transport::get();
        return response()->json([
           'list' => $Transport
        ]);
    }
    public function getAllDealer(){
        $dealer = $this->loadCustomer();
        return response()->json([
           'dealer' => $dealer
        ]);
    }
    public function getChallanInformation(Request $request)
    {
        $ChallanNo =$request->ChallanNo;
        $challan = ChallanMaster::select('CustomerCode', 'TransportName', 'DriverName', 'DriverPhoneNo', 'TransportNo' )
            ->where('ChallanNo','=',$ChallanNo)->first();

        if ($request->CustomerCode)
        {
            $customerCode=$request->CustomerCode;
        }
        $customerCode=$challan->CustomerCode;


        $numbers = DB::select("SELECT C.CustomerCode , C.CustomerName ,
              CASE WHEN D.CustomerMobile IS NOT NULL THEN D.CustomerMobile ELSE 
                    CASE WHEN Phone = '' THEN RIGHT(REPLACE(Mobile, '-',''),11) ELSE RIGHT(REPLACE(Mobile, '-',''),11) END END Phone,
                ISNULL(D.MOTMNumber,'') AS MOTMNumber
            FROM Customer C
                LEFT JOIN (SELECT TOP 1 * FROM TransportNotification WHERE CustomerCode = '$customerCode' AND MOTMNumber <> '' ORDER BY NotificationID DESC) D
                    ON C.CustomerCode = D.CustomerCode
            WHERE C.CustomerCode = '$customerCode'
                    ");

            return response()->json([
                'challans'=>$challan,
                'numbers'=>$numbers
            ]);

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ChallanNo' => 'required',
            'CustomerCode' => 'required',
            'TruckNo' => 'required',
            'DriverContactNo' => 'required',
            'DeliveryDate' => 'required',
            'DeliveryTime' => 'required',
            'MOTMNumber' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }
        $userId = Auth::user()->UserId;
        $number =$request->CustomerMobile.','.$request->CustomerMobile;
        $EntryDATE = Carbon::now();
        $customer =$request->CustomerCode;
        try {
            if($request->ActionType== "add"){
                $input          = $request->except(['ActionType','EditedBy','EditedDATE','EditedIP']);
                $input= array_merge(
                    $input,
                    ['EntryBy'     => Auth::user()->UserId],
                    ['EntryDATE'     => Carbon::now()],
                    ['EntryIP'     => \request()->ip()],
                    ['TransportID'     => $request->TransportID],

                );

                    if (Notification::create($input)) {
                        $message = "A truck has been released at your destination. Probable delivery schedule :{$request->DeliveryTime}.%0D%0ADriver Information. %0D%0A%0D%0ADriver mobile : {$request->DriverContactNo }. %0D%0ATruck No : {$request->TruckNo}. %0D%0A%0D%0AFor more information please log in YAMAHA DMS. %0D%0A%0D%0AThanks %0D%0AACI Motors ";
                        $this->sendSmsQ(
                            $number, 8809617614917, 'Yamaha-DMS', 'Transport Notification',
                            'Yamaha',$userId, 'smsq', $message
                        );
                        return response()->json([
                            'status' => 'Success',
                            'message' => 'Transport Notification Added Successfully'
                        ],200);
                    }

            }
            else{


                $NotificationID      = $request->NotificationID;
                $updateArray    = $request->except(['ActionType','NotificationID','EntryBy','EntryDATE','EntryIP']);
                $updateArray= array_merge(
                    $updateArray,
                    ['EditedBy'     => Auth::user()->UserId],
                    ['EditedDATE'     => Carbon::now()],
                    ['EntryIP'     => \request()->ip()],
                    ['TransportID'     => $request->TransportID],
                );
                if (Notification::where('NotificationID', $NotificationID)->update($updateArray)) {
                    $message = "A truck has been released at your destination. Probable delivery schedule :{$request->DeliveryTime}.%0D%0ADriver Information. %0D%0A%0D%0ADriver mobile : {$request->DriverContactNo }. %0D%0ATruck No : {$request->TruckNo}. %0D%0A%0D%0AFor more information please log in YAMAHA DMS. %0D%0A%0D%0AThanks %0D%0AACI Motors ";
                    $this->sendSmsQ(
                        $number, 8809617614917, 'Yamaha-DMS', 'Transport Notification',
                        'Yamaha',$userId, 'smsq', $message
                    );
                    return response()->json([
                        'status' => 'Success',
                        'message' => 'Transport Notification Updated Successfully'
                    ],200);
                }
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function show($notificationID){
        $list = DB::table('TransportNotification as t')
            ->select('t.*','tp.TransportName','c.CustomerName')
            ->join('Transport as tp ','tp.TransportID','=','t.TransportID')
            ->join('Customer as c','c.CustomerCode','=','t.CustomerCode')
            ->where('NotificationID',$notificationID)
            ->first();

        return response()->json([
            'list'=> $list
        ]);
    }


}
