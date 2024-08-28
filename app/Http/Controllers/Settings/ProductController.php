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
            'Product.Business'
        )->where(function ($q) use ($search) {
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
}
