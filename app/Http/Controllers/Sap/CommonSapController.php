<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonSapController extends Controller
{
    public function storeSapProduct(Request $request)
    {
        try {
            $requestProducts = $request->all();
            $productCount = 0;
            foreach ($requestProducts as $singleProduct) {
                if (!empty($singleProduct['ProductCode']) && !empty($singleProduct['ProductName']) && !empty($singleProduct['BrandCode'])
                    && !empty($singleProduct['UnitPrice']) && !empty($singleProduct['VAT']) && !empty($singleProduct['MRP'])
                    && !empty($singleProduct['Business']) && !empty($singleProduct['Active'])) {

                    //Check Already Exist Or Not
                    $checkExisting =  Product::where('ProductCode',$singleProduct['ProductCode'])->first();
                    if(!empty($checkExisting->ProductCode)){
                        file_put_contents('public/log/sap/sap_product_file_already_exist.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Already Exist',
                            'ErrorProduct'=>$singleProduct
                        ], 409);
                    }
                    else{
                        $productCount +=1;
                        DB::beginTransaction();

                        //Product Create
                        $product = new Product();
                        $product->ProductCode = $singleProduct['ProductCode'];
                        $product->ProductName = $singleProduct['ProductName'];
                        $product->PackSize = $singleProduct['PackSize'];
                        $product->BrandCode = $singleProduct['BrandCode'];
                        $product->UnitPrice = $singleProduct['UnitPrice'];
                        $product->VAT = $singleProduct['VAT'];
                        $product->MRP = $singleProduct['MRP'];
                        $product->Business = $singleProduct['Business'];
                        $product->Active = $singleProduct['Active'];
                        $product->save();
                        DB::commit();
                    }
                } else {
                    DB::rollBack();
                    file_put_contents('public/log/sap/sap_product_file_not_acceptable_create.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not Acceptable ! Check Format Or Empty Value',
                        'ErrorProduct'=>$singleProduct
                    ], 406);
                }
            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Product Added Successfully',
                'ProductCount'=>$productCount
            ], 200);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            file_put_contents('public/log/sap/sap_product_error.txt', $exception->getMessage() . "\n", FILE_APPEND);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage().'-'.$exception->getLine()
            ], 500);
        }
    }
    public function storeSapCustomer(Request $request)
    {
        dd($request);
        try {
            $requestCustomers = $request->all();
            $customerCount = 0;
            foreach ($requestCustomers as $singleCustomer) {
                if (!empty($singleCustomer['ProductCode']) && !empty($singleProduct['ProductName']) && !empty($singleProduct['BrandCode'])
                    && !empty($singleCustomer['UnitPrice']) && !empty($singleProduct['VAT']) && !empty($singleProduct['MRP'])
                    && !empty($singleCustomer['Business']) && !empty($singleProduct['Active'])) {

                    //Check Already Exist Or Not
                    $checkExisting =  Product::where('ProductCode',$singleProduct['ProductCode'])->first();
                    if(!empty($checkExisting->ProductCode)){
                        file_put_contents('public/log/sap/sap_product_file_already_exist.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Already Exist',
                            'ErrorProduct'=>$singleProduct
                        ], 409);
                    }
                    else{
                        $customerCount +=1;
                        DB::beginTransaction();

                        //Product Create
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
                        DB::commit();
                    }
                } else {
                    DB::rollBack();
                    file_put_contents('public/log/sap/sap_product_file_not_acceptable_create.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not Acceptable ! Check Format Or Empty Value',
                        'ErrorProduct'=>$singleProduct
                    ], 406);
                }
            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Product Added Successfully',
                'ProductCount'=>$customerCount
            ], 200);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            file_put_contents('public/log/sap/sap_product_error.txt', $exception->getMessage() . "\n", FILE_APPEND);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage().'-'.$exception->getLine()
            ], 500);
        }
    }
}
