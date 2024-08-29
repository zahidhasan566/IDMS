<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use CodeGeneration;
    use CommonTrait;
    public function index(Request $request){

        $CurrentPage = $request->pagination['current_page'];
        $PerPage     = 20;
        $Export      = $request->Export;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "EXEC doLoadCustomerList '','$PerPage','$CurrentPage'";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function store(Request $request){
        $this->validate($request,[
           'CustomerCode' => 'required',
           'CustomerName' => 'required',
           'Gender' => 'required',
           'Add1' => 'required',
           'DistrictCode' => 'required',
           'Mobile' => 'required',
           'ThanaCode' => 'required',
           'NID' => 'required',
           'CustomerType' => 'required',
           'PaymentMode' => 'required',
        ]);

        $existCustomerCheck = Customer::query()->where('CustomerCode',$request->CustomerCode)->exists();
        if ($existCustomerCheck){
            return response()->json([
                'status'    => 'success',
                'message'   => 'Customer Already Exist'
            ]);
        }

        try {
            $customer = new Customer();
            $customer->CustomerCode     = $request->CustomerCode;
            $customer->CustomerName     = $request->CustomerName;
            $customer->Gender           = $request->Gender;
            $customer->Add1             = $request->Add1;
            $customer->Add2             = $request->Add2 ? $request->Add2 : '';
            $customer->DistrictCode     = $request->DistrictCode;
            $customer->ThanaCode        = $request->ThanaCode;
            $customer->Email            = $request->Email;
            $customer->ContactPerson    = $request->ContactPerson ? $request->ContactPerson : '';
            $customer->Phone            = '';
            $customer->Mobile           = $request->Mobile;
            $customer->ThanaCode        = $request->ThanaCode;
            $customer->NID              = $request->NID;
            $customer->Business         = $request->Business ? $request->Business : '';
            $customer->DepotCode        = $request->DepotCode ? $request->DepotCode : '';
            $customer->CustomerType     = $request->CustomerType;
            $customer->PaymentMode      = $request->PaymentMode;
            $customer->CreditDays       = '';
            $customer->CreditLimit      = '';
            $customer->Metro            = '';
            $customer->RouteCode        = '';
            $customer->TTYCode          = '';
            $customer->UnionCode        = '';
            $customer->HatBazarCode     = '';
            $customer->CreateDate      = '';
            $customer->EditDate         = '';
            $customer->DiscountStatus   = '';
            $customer->Active           = 'Y';
            $customer->Fifo             = 'Y';
            $customer->Message          = '';
            $customer->Category         = '';
            $customer->RegistrationName = '';
            $customer->RegistrationAdd1 = '';
            $customer->RegistrationAdd2 = '';
            $customer->FathersName      = '';
            $customer->DOB              = null;
            $customer->OwnerType        = '';
            $customer->SubBusinessCode  = '';
            $customer->save();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Successfully Created'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function edit(Request $request){
        $customer = Customer::query()->where('CustomerCode',$request->CustomerCode)->first();
        return response()->json([
           'customer' => $customer
        ]);
    }

    public function update(Request $request){
        $this->validate($request,[
            'CustomerCode' => 'required',
            'CustomerName' => 'required',
            'Gender' => 'required',
            'Add1' => 'required',
            'DistrictCode' => 'required',
            'Mobile' => 'required',
            'ThanaCode' => 'required',
            'NID' => 'required',
            'CustomerType' => 'required',
            'PaymentMode' => 'required',
        ]);

        try {

            $customer = Customer::query()->where('CustomerCode',$request->CustomerCode)->first();
            $customer->CustomerCode     = $request->CustomerCode;
            $customer->CustomerName     = $request->CustomerName;
            $customer->Gender           = $request->Gender;
            $customer->Add1             = $request->Add1;
            $customer->Add2             = $request->Add2 ? $request->Add2 : '';
            $customer->DistrictCode     = $request->DistrictCode;
            $customer->ThanaCode        = $request->ThanaCode;
            $customer->Email            = $request->Email ? $request->Email : '';
            $customer->ContactPerson    = $request->ContactPerson ? $request->ContactPerson : '';
            $customer->Phone            = '';
            $customer->Mobile           = $request->Mobile;
            $customer->ThanaCode        = $request->ThanaCode;
            $customer->NID              = $request->NID;
            $customer->Business         = $request->Business ? $request->Business : '';
            $customer->DepotCode        = $request->DepotCode ? $request->DepotCode : '';
            $customer->CustomerType     = $request->CustomerType;
            $customer->PaymentMode      = $request->PaymentMode;
            $customer->CreditDays       = '';
            $customer->CreditLimit      = '';
            $customer->Metro            = '';
            $customer->RouteCode        = '';
            $customer->TTYCode          = '';
            $customer->UnionCode        = '';
            $customer->HatBazarCode     = '';
            $customer->CreateDate       = Carbon::now();
            $customer->EditDate         = '';
            $customer->DiscountStatus   = '';
            $customer->Active           = 'Y';
            $customer->Fifo             = 'Y';
            $customer->Message          = '';
            $customer->Category         = '';
            $customer->RegistrationName = '';
            $customer->RegistrationAdd1 = '';
            $customer->RegistrationAdd2 = '';
            $customer->FathersName      = '';
            $customer->DOB              = null;
            $customer->OwnerType        = '';
            $customer->SubBusinessCode  = '';
            $customer->save();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Successfully Created'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function getAllCustomerType(){
        $customerType = CustomerType::query()->get();
        return response()->json([
           'customerType' => $customerType
        ]);
    }
}
