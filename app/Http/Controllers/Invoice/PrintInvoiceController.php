<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\DealarInvoiceEditLog;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrintInvoiceController extends Controller
{
    use CommonTrait;
    public function getChassisNoInfo(Request $request){
        $chassisNo = $request->chassisNo;

        try {
            $data = DB::table('DealarInvoiceMaster as dim')->select(
                'ChassisNo','EngineNo','InvoiceDate','dim.InvoiceId','ProductName','UnitPrice','Discount','Color','FuelUsed','RPM','CubicCapacity',
                'WheelBase','Weight','TireSizeFront','TireSizeRear','Seats','NoofTyre','NoofAxel','ClassOfVehicle','MakerName','MakerCountry','EngineType',
                'ImportYear','CustomerName','MobileNo as CustomerMobile','EMail as CustomerEmail','FatherName','MotherName','PerAddress as PermanentAddress',
                'PreAddress as PresentAddress','NID')
                ->join('DealarInvoiceDetails as did','did.InvoiceID','=','dim.InvoiceID')
                ->where('did.ChassisNo','=',$chassisNo)->get();
            return response()->json([
                'status'=>'success',
                'data'=>$data
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
    public function storeInvoicePrint(Request $request){
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'invoiceId' => 'required',
                'mobile' => 'required',
                'customerName' => 'required',
                'fatherName' => 'required',
                'motherName' => 'required',
                'permanentAddress' => 'required',
                'presentAddress' => 'required',
                'NID' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 401, 'error' => $validator->errors()]);
            }

            $invoiceId = $request->invoiceId;
            $mobile = $request->mobile;
            $customerName = $request->customerName;
            $customerMail = $request->customerMail;
            $fatherName = $request->fatherName;
            $motherName = $request->motherName;
            $permanentAddress = $request->permanentAddress;
            $presentAddress = $request->presentAddress;
            $NID = $request->NID;

            //Invoice Master
            $previous = DealarInvoiceMaster::where('InvoiceId',$invoiceId)->first();
            $master = DealarInvoiceMaster::where('InvoiceId',$invoiceId)->first();
            $master->MobileNo = $mobile;
            $master->FatherName = $fatherName;
            $master->MotherName = $motherName;
            $master->EMail = $customerMail;
            $master->CustomerName = $customerName;
            $master->PerAddress = $permanentAddress;
            $master->PreAddress = $presentAddress;
            $master->NID = $NID;
            $master->save();

            //Edit Log
            $log =  new DealarInvoiceEditLog();
            $log->InvoiceId = $invoiceId;
            $log->MobileNo = $mobile;
            $log->FatherName = $fatherName;
            $log->MotherName = $motherName;
            $log->EMail = $customerMail;
            $log->PreviousCustomerName = $previous->CustomerName;
            $log->CurrentCustomerName = $customerName;
            $log->PerAddress = $permanentAddress;
            $log->PreAddress = $presentAddress;
            $log->PreviousNID = $previous->NID;
            $log->CurrentNID = $NID;
            $log->EditBy = Auth::user()->UserId;
            $log->EditDate = Carbon::now();
            $log->IpAddress = $request->ip();
            $log->save();


            DB::commit();
            return response()->json([
                'status'=>'success',
                'message'=>'Data has successfully updated',
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }

    }
}
