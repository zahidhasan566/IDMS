<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\ProdBrand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request){
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $products = Product::select(
            'Product.ProductCode',
            'Product.ProductName',
            'Product.PartBrand',
            'Product.PartNo',
            'Product.PackSize',
            'Product.BrandCode',
            'Product.ProductCategory',
            'Product.GroupCode',
            'Product.Model',
            'Product.UnitPrice',
            'Product.DistDiscount',
            'Product.VAT',
            'Product.MRP',
            'Product.Active',
            'Business.BusinessName'
        )
            ->join('Business','Business.Business','Product.Business')
            ->where(function ($q) use ($search) {
                $q->where('Product.ProductCode', 'like', '%' . $search . '%');
                $q->Orwhere('Product.ProductName', 'like', '%' . $search . '%');
                $q->Orwhere('Product.PartNo', 'like', '%' . $search . '%');
            });

        if ($request->type === 'export') {
            return response()->json([
                'data' => $products->get(),
            ]);
        } else {
            return $products->paginate($take);
        }
    }

    public function supportingData(){
        $businesses = Business::select('Business as id',DB::raw("CONCAT(Business,'-',BusinessName) AS BusinessName"))->get();
        $brand = ProdBrand::select('BrandCode as id',DB::raw("CONCAT(BrandCode,'-',BrandName) AS BrandName"))->get();
        return response()->json([
            'businesses' => $businesses,
            'brand' => $brand,
        ]);

    }
    public function store(Request $request){

        try{
            //Check Already Exist Or Not
            $checkExisting =  Product::where('ProductCode',$request->productCode)->first();
            if(!empty($checkExisting->ProductCode)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product Already Exist',
                ], 409);
            }
            else{
                $product = new Product();
                $product->ProductCode = $request->productCode;
                $product->ProductName = $request->productName;
                $product->PackSize = $request->pacSize;
                $product->BrandCode = $request->brandSelect['id'];
                $product->UnitPrice = $request->unitPrice;
                $product->VAT = $request->vat;
                $product->MRP = $request->mrp;
                $product->Business = $request->businessSelect['id'];
                $product->Active = $request->active;
                $product->save();

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Product Added Successfully',
                ],200);
            }

        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
    public function getExistingProduct($productCode){
        $existingProduct = Product::select('Product.*','Business.BusinessName','ProdBrand.BrandName')
                            ->join('Business','Business.Business','Product.Business')
                            ->join('ProdBrand','ProdBrand.BrandCode','Product.BrandCode')
                            ->where('ProductCode',$productCode)
                            ->first();
        return response()->json([
            'existingProduct' => $existingProduct,
        ]);
    }
    public function updateProduct(Request $request){
        try{
            if($request->existingProductCode){
                Product::where('ProductCode',$request->existingProductCode)->update([
                  'ProductCode' => $request->productCode,
                  'ProductName' => $request->productName,
                  'PackSize' => $request->pacSize,
                  'BrandCode' =>$request->brandSelect['id'],
                  'UnitPrice' =>$request->unitPrice,
                  'VAT' =>$request->vat,
                  'MRP' =>$request->mrp,
                  'Business' =>$request->businessSelect['id'],
                  'Active' =>$request->active,
                ]);
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Product Updated Successfully',
                ],200);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product Code Not Found'
                ], 404);
            }

        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }

        dd($request);
    }
}
