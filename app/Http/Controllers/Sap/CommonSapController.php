<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
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
        try {
            $requestCustomers = $request->all();
            $customerCount = 0;
            foreach ($requestCustomers as $singleCustomer) {
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
}
