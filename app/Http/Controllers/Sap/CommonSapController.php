<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sap\SapUserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommonSapController extends Controller
{
    protected $id = 0;

    public function __construct()
    {
        $this->createSapUserLog();
    }

    public function createSapUserLog()
    {
        $sapUser = new SapUserLog();
        $sapUser->EntryBy = Auth::user()->UserId;
        $sapUser->EntryDate = Carbon::now();
        $sapUser->EntryIpAddress = $_SERVER['REMOTE_ADDR'];
        $sapUser->Status = 'Pending';
        $sapUser->save();
        $this->id = $sapUser->Id;

    }

    public function storeSapProduct(Request $request)
    {
        $dt = date('Y-m-d H-i-s A');
        try {
            $requestProducts = $request->all();
            $productCount = 0;


            foreach ($requestProducts as $singleProduct) {
                if (!empty($singleProduct['ProductCode']) && !empty($singleProduct['ProductName']) && !empty($singleProduct['BrandCode'])
                    && !empty($singleProduct['UnitPrice']) && !empty($singleProduct['VAT']) && !empty($singleProduct['MRP'])
                    && !empty($singleProduct['Business']) && !empty($singleProduct['Active'])) {

                    //Check Already Exist Or Not
                    $checkExisting = Product::where('ProductCode', $singleProduct['ProductCode'])->first();
                    if (!empty($checkExisting->ProductCode)) {
                        file_put_contents('public/log/sap/sap_product_file_already_exist-' . $dt . '.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                        $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Product', 'Status' => 'Failed','Reason'=>'Already Exist']);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Already Exist',
                            'ErrorProduct' => $singleProduct
                        ], 409);
                    } else {
                        $productCount += 1;
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
                    file_put_contents('public/log/sap/sap_product_file_not_acceptable_create-' . $dt . '.txt', json_encode($singleProduct) . "\n", FILE_APPEND);
                    //SAP USER  LOG
                    $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Product', 'Status' => 'Failed','Reason'=>'Format Or Empty Or Existing Issue']);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not Acceptable ! Check Format Or Empty Or Existing Value',
                        'ErrorProduct' => $singleProduct
                    ], 406);
                }
            }

            //SAP USER LOG
            $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Product', 'Status' => 'Done']);
            return response()->json([
                'status' => 'Success',
                'message' => 'Product Added Successfully',
                'ProductCount' => $productCount
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            file_put_contents('public/log/sap/sap_product_error-' . $dt . '.txt', $exception->getMessage() . '-' . $exception->getLine() . "\n", FILE_APPEND);
            $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Product', 'Status' => 'Failed','Reason'=>$exception->getMessage() . '-' . $exception->getLine()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    public function storeSapCustomer(Request $request)
    {
        $dt = date('Y-m-d H-i-s A');
        try {
            $requestCustomers = $request->all();
            $customerCount = 0;

            foreach ($requestCustomers as $singleCustomer) {
                if (!empty($singleCustomer['CustomerCode']) && !empty($singleCustomer['CustomerName']) && !empty($singleCustomer['DistrictCode'])
                    && !empty($singleCustomer['Gender']) && !empty($singleCustomer['Add1']) && !empty($singleCustomer['Email'])
                    && !empty($singleCustomer['Mobile']) && !empty($singleCustomer['Thana'])
                    && !empty($singleCustomer['NID']) && !empty($singleCustomer['CustomerType']) && !empty($singleCustomer['PaymentMode'])) {
                    //Check Already Exist Or Not
                    $existCustomerCheck = Customer::query()->where('CustomerCode', $singleCustomer['CustomerCode'])->exists();
                    if ($existCustomerCheck) {
                        file_put_contents('public/log/sap/sap_customer_file_already_exist-' . $dt . '.txt', json_encode($singleCustomer) . "\n", FILE_APPEND);
                        $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Customer', 'Status' => 'Failed','Reason'=>'Already Exist']);
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Customer Already Exist',
                            'ErrorCustomer' => $singleCustomer
                        ], 409);
                    } else {
                        $customerCount += 1;
                        DB::beginTransaction();

                        //Customer Create
                        $customer = new Customer();
                        $customer->CustomerCode = $singleCustomer['CustomerCode'];
                        $customer->CustomerName = $singleCustomer['CustomerName'];
                        $customer->Gender = $singleCustomer['Gender'];
                        $customer->Add1 = $singleCustomer['Add1'];
                        $customer->Add2 = $singleCustomer['Add2'];
                        $customer->DistrictCode = $singleCustomer['DistrictCode'];
                        $customer->ThanaCode = $singleCustomer['Thana'];
                        $customer->Email = $singleCustomer['Email'];
                        $customer->ContactPerson = $singleCustomer['ContactPerson'];
                        $customer->Phone = '';
                        $customer->Mobile = $singleCustomer['Mobile'];
                        $customer->ThanaCode = $singleCustomer['Thana'];
                        $customer->NID = $singleCustomer['NID'];
                        $customer->Business = $singleCustomer['Business'];
                        $customer->DepotCode = '';
                        $customer->CustomerType = $singleCustomer['CustomerType'];
                        $customer->PaymentMode = $singleCustomer['PaymentMode'];
                        $customer->CreditDays = '';
                        $customer->CreditLimit = '';
                        $customer->Metro = '';
                        $customer->RouteCode = '';
                        $customer->TTYCode = '';
                        $customer->UnionCode = '';
                        $customer->HatBazarCode = '';
                        $customer->CreateDate = '';
                        $customer->EditDate = '';
                        $customer->DiscountStatus = '';
                        $customer->Active = 'Y';
                        $customer->Fifo = 'Y';
                        $customer->Message = '';
                        $customer->Category = '';
                        $customer->RegistrationName = '';
                        $customer->RegistrationAdd1 = '';
                        $customer->RegistrationAdd2 = '';
                        $customer->FathersName = '';
                        $customer->DOB = '';
                        $customer->OwnerType = '';
                        $customer->SubBusinessCode = '';
                        $customer->save();
                        DB::commit();
                    }
                } else {
                    DB::rollBack();
                    file_put_contents('public/log/sap/sap_customer_file_not_acceptable_create-' . $dt . '.txt', json_encode($singleCustomer) . "\n", FILE_APPEND);
                    $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Customer', 'Status' => 'Failed','Reason'=>'Format Or Empty Or Existing Issue']);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not Acceptable ! Check Format Or Empty Or Existing Value',
                        'ErrorProduct' => $singleCustomer
                    ], 406);
                }
            }
            $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Customer', 'Status' => 'Done']);
            return response()->json([
                'status' => 'Success',
                'message' => 'Customer Added Successfully',
                'CustomerCount' => $customerCount
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            file_put_contents('public/log/sap/sap_customer_error-' . $dt . '.txt', $exception->getMessage() . '-' . $exception->getLine() . "\n", FILE_APPEND);
            $sapUserLog = SapUserLog::where('Id', $this->id)->update(['ApiType' => 'Customer', 'Status' => 'Failed','Reason'=>$exception->getMessage() . '-' . $exception->getLine()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }
}
