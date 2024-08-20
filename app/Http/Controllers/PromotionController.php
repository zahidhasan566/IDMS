<?php

namespace App\Http\Controllers;

use App\Models\ProductPromoDetail;
use App\Models\Promotion;
use App\Services\SpPaginationService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    use CommonTrait;
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $query = Promotion::join('ProdBrand', 'ProdBrand.BrandCode', 'ProductPromo.BrandCode')
            ->where('ProductPromo.Business', 'C');
        if ($search !== '') {
            $query->where('ProductPromo.PromoName','like','%'.$search.'%');
        }
        return $query->orderBy('EntryDate', 'desc')
            ->select('ProductPromo.PromoId', 'ProductPromo.PromoName', 'ProductPromo.PromoStartDate as StartDate', 'ProductPromo.PromoEndDate as EndDate', 'ProdBrand.BrandName', 'ProdBrand.BrandCode', 'ProductPromo.Active as Status', 'ProductPromo.EntryDate')
            ->paginate($take);
    }
    public function supportData()
    {
        return response()->json([
            'status' => 'success',
            'data' => DB::table('ProdBrand')->where('Business','C')->select('BrandCode','BrandName')->get()
        ]);
    }
    public function getProductByBrand($brandCode)
    {
        return response()->json([
            'data' => DB::table('Product')->where('BrandCode',$brandCode)->select('ProductCode','ProductName')->get()
        ]);
    }
    public function getPromotionInfo($promoId)
    {
        return response()->json([
            'data' => Promotion::where('PromoId',$promoId)->select('PromoId','PromoName',DB::raw("CONVERT(date,PromoStartDate) as PromoStartDate"),DB::raw("CONVERT(date,PromoStartDate) as PromoEndDate"),'BrandCode','SalesQnty','Amount','Amount')->with('details')->first()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'promoName' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'brand' => 'required',
            'product' => 'required'
        ]);
        DB::beginTransaction();
        try {
            if (count($request->product)) {
                $promo = Promotion::create([
                    'PromoName' => $request->promoName,
                    'PromoStartDate' => $request->startDate,
                    'PromoEndDate' => $request->endDate,
                    'PromoPeriod' => date('Ym'),
                    'BrandCode' => $request->brand,
                    'SalesQnty' => $request->quantity,
                    'Amount' => $request->amount,
                    'Active' => 1,
                    'Business' => 'C',
                    'EntryBy' => Auth::user()->UserId,
                    'EntryDate' => date('Y-m-d H:i:s'),
                    'EntryIpAddress' => $request->ip(),
                ]);
                foreach ($request->product as $product) {
                    ProductPromoDetail::create([
                        'PromoId' => $promo->PromoId,
                        'ProductCode' => $product['id']
                    ]);
                }
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Promotion created successfully.'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'No Products!'
            ],404);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }
    public function update(Request $request, $promoId)
    {
        $request->validate([
            'promoName' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'brand' => 'required',
            'product' => 'required'
        ]);
        DB::beginTransaction();
        try {
            if (count($request->product)) {
                Promotion::where('PromoId',$promoId)->update([
                    'PromoName' => $request->promoName,
                    'PromoStartDate' => $request->startDate,
                    'PromoEndDate' => $request->endDate,
                    'PromoPeriod' => date('Ym'),
                    'BrandCode' => $request->brand,
                    'SalesQnty' => $request->quantity,
                    'Amount' => $request->amount,
                    'Active' => 1,
                    'Business' => 'C',
                    'EditedBy' => Auth::user()->UserId,
                    'EditedDate' => date('Y-m-d H:i:s'),
                    'EditedIpAddress' => $request->ip(),
                ]);
                ProductPromoDetail::where('PromoId',$promoId)->delete();
                foreach ($request->product as $product) {
                    ProductPromoDetail::create([
                        'PromoId' => $promoId,
                        'ProductCode' => $product['id']
                    ]);
                }
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Promotion updated successfully.'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'No Products!'
            ],404);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }
    public function report(Request $request)
    {
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page,$take);
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $type = $request->type;
        if (!empty($startDate) && !empty($endDate)) {
            $roleId = Auth::user()->RoleId;
            $userId = Auth::user()->UserId;
            if ($roleId !== 'customer') {
                if (!empty($request->customer)) {
                    $customer = $request->customer;
                } else {
                    $customer = '';
                }
            } else {
                $customer = $userId;
            }
            if ($type === 'export') {
                $sp = "exec sp_promotion_report '$startDate','$endDate','$customer',$take,$offset,'Y'";
                return response()->json([
                    'data' => SpPaginationService::getPdoResult($sp)
                ]);
            } else {
                $sp = "exec sp_promotion_report '$startDate','$endDate','$customer',$take,$offset,'N'";
            }
            return SpPaginationService::paginate2($sp,$take,$offset);
        }
        return response()->json([
            'status' => 'error',
            'data' => [[]]
        ]);
    }

    public function topSheet(Request $request)
    {
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page,$take);
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $type = $request->type;
        if (!empty($startDate) && !empty($endDate)) {
            $roleId = Auth::user()->RoleId;
            $userId = Auth::user()->UserId;
            if ($roleId !== 'customer') {
                if (!empty($request->customer)) {
                    $customer = $request->customer;
                } else {
                    $customer = '';
                }
            } else {
                $customer = $userId;
            }
            if ($type === 'export') {
                $sp = "exec sp_promotion_top_sheet '$startDate','$endDate','$customer',$take,$offset,'Y'";
                return response()->json([
                    'data' => SpPaginationService::getPdoResult($sp)
                ]);
            } else {
                $sp = "exec sp_promotion_top_sheet '$startDate','$endDate','$customer',$take,$offset,'N'";
            }
            return SpPaginationService::paginate2($sp,$take,$offset);
        }
        return response()->json([
            'status' => 'error',
            'data' => [[]]
        ]);
    }

    public function customers()
    {
        return response()->json([
            'data' => $this->loadCustomer()
        ]);
    }
}
