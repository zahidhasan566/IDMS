<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerMapping;
use App\Models\PaymentTempOnline;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PaymentController extends Controller
{
    use CommonTrait;
    use CodeGeneration;
    public function index(Request $request){

        $customerCode = $request->customerCode;
        $business = 'C';
        $admin = Auth::user()->grpAdd;
        $fromDate = $request->fromDate;
        $toDate = $request->toDate . ' 23:59:59';
        $roleId = Auth::user()->RoleId;

        $payment = DB::table('PaymentTempOnline as PTO')->Select(
            'PTO.CustomerCode as DealerCode',
            'PTO.MoneyRecNoTemp',
            'PTO.Approved',
            'd.DepotName',
            'bs.BusinessName',
            DB::raw("convert(date,PTO.PaymentDate) as PaymentDate "),
            'PTO.PaymentAmount',
            'PTO.SalesType',
            'c.CustomerName',
            'PTO.PaymentMode',
            'PTO.Reference',
            'PTO.ChequeNo',
            'b.BankName',
            DB::raw("convert(date,PTO.ChequeDate) as ChequeDate "),
            'PTO.CheqBranch',
            DB::raw("convert(date,PTO.PreparedDate) as PreparedDate "),
            'PTO.ChequeImage',
            )
            ->join('Banks as b','b.BankCode','PTO.BankCode')
            ->join('Business as bs','bs.Business','PTO.Business')
            ->leftJoin('Depot as d','d.DepotCode','PTO.DepotCode')
            ->leftjoin('Customer as c','c.CustomerCode','PTO.CustomerCode')
            ->where('PTO.Business',$business)
            ->whereNotNull('PTO.ChequeImage')
            ->wherebetween('PTO.PaymentDate',[$fromDate, $toDate]);

            $roleId = Auth::user()->RoleId;

            if ($roleId !== 'admin') {
                $payment->where('PTO.CustomerCode', Auth::user()->UserId);
            }
            return response()->json([
                'data'=>$payment->get()
            ]);

    }

    public function getCustomerWiseBusiness(){
        $business= $this->customerWiseBusiness();
        return response()->json([
            'data' => $business,
        ]);
    }
    public function getBank(){
        $bank= $this->bankList();
        return response()->json([
            'data' => $bank,
        ]);
    }
    public function getCustomerCode(){
        $customers= $this->loadCustomer();
        return response([
            'data'=>$customers
        ]);
    }
    public function getSalesType(Request $request){
        $businessCode =$request->businessCode;
        $customerInfo =CustomerMapping::select('CustomerCode')->where('CustomerMasterCode',Auth::user()->UserId)->where('Business',$businessCode)->get();
        $customerCode =$customerInfo[0]->CustomerCode;
        $customers= $this->salesType($customerCode);
        return response([
            'data'=>$customers
        ]);
    }
    public function getCustomerList(){

        $user = Auth::user()->grpAdd;
        if ($user == 0){
            $customerID =Auth::user()->UserId;
        }else{
            $customerID ='%';

        }
        $customerList= $this->loadCustomer();

        return response()->json([
            'data' => $customerList,
        ]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'reference' => 'required',
            'bankCode' => 'required',
            'chequeDate' => 'required',
            'chequeImage' => 'required',
            'payment' => 'required',
            'paymentMode' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'error' => $validator->errors()],500);
        }

        if ($request->paymentMode=== 'Deposit Slip'){
            $validator = Validator::make($request->all(), [
                'chequeNo' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error',
                    'message' =>'Cheque No. is required'],500);
            }
        }

        $customer = Customer::where('CustomerCode',$request->customer['CustomerCode'])->first();

        $depotCode = !empty($customer->bankCode['DepotCode'])?$customer->bankCode['DepotCode']:'H';
        $business = $request->businessCode;
        $SalesType =$customer->PaymentMode;
        $CustomerCode = $request->customer['CustomerCode'];
        $CustomerMasterCode = $request->customer['CustomerCode'];
        $PreparedDate =Carbon::now()->format('Y-m-d');
        $bankCode =$request->bankCode['BankCode'];
        $paymentAmount = $request->payment;
        $PaymentDate = date("Y-m-d 00:00:00.000");
        $entryExist = PaymentTempOnline::where('PaymentDate','=',$PaymentDate)
            ->where('CustomerCode','=',$CustomerCode)
            ->where('BankCode','=',$bankCode)
            ->where('PaymentAmount','=',$paymentAmount)->first();

        if (!empty($entryExist)){
            return response()->json([
                'status' => 'Error',
                'message'=>'This Entry Already Exist!'
            ],500);
        }else{
            try{

                //Store
                DB::beginTransaction();
                $moneyReceiptNo = $this->getMoneyReceiptNo($depotCode,$business);
                $moneyReceiptNo = $moneyReceiptNo[0]->MoneyReceiptNo;
                $checkno = $request->chequeNo != null ?$request->chequeNo : '';

                if ($request->has('chequeImage')) {

                    $image = $request->chequeImage;
                    $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                    $img=Image::make($image)->resize('900')->save(public_path('uploads/payment/') . $name);
                } else {
                    $name = 'not_found.jpg';
                }
                $payment = new PaymentTempOnline();
                $payment->MoneyRecNoTemp = $moneyReceiptNo;
                $payment->DepotCode = '';
                $payment->Business = $business;
                $payment->PaymentDate = $PaymentDate ;
                $payment->SalesType = $SalesType;
                $payment->CustomerCode =$CustomerCode;
                $payment->PaymentAmount = $paymentAmount;
                $payment->PaymentMode = $request->paymentMode;
                $payment->Reference = $request->reference;
                $payment->ChequeNo = $checkno;
                $payment->ChequeDate = $request->chequeDate;
                $payment->BankCode = $bankCode;
                $payment->CheqBranch = $request->branch;
                $payment->PreparedBy = $CustomerMasterCode;
                $payment->PreparedDate =$PreparedDate;
                $payment->Period = Carbon::now()->format('Ym');
                $payment->ChequeImage = $name;
                $payment->save();
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message'=>'Credit Payment Stored Successfully'
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

    }

}
