<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\JobCard\TblJobCard;
use App\Models\Product;
use App\Models\ReturnScrappedProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrapController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $returnScrapProducts = ReturnScrappedProducts::select(
            'ReturnScrappedProducts.ScrapID',
            'ReturnScrappedProducts.ProductCode',
            'Product.ProductName',
            'ReturnScrappedProducts.RequestToReturnQnty',
            'ReturnScrappedProducts.Reason',
            'ReturnScrappedProducts.ApproveQnty',
            'ReturnScrappedProducts.UnitPrice',
            'ReturnScrappedProducts.Vat',
            'ReturnScrappedProducts.Total',
            'ReturnScrappedProducts.RequestedBy',
            DB::raw('convert(date,ReturnScrappedProducts.RequestedDate) as RequestedDate'),
            'ReturnScrappedProducts.ApprovedBy',
            DB::raw('convert(date,ReturnScrappedProducts.ApprovedDate) as ApprovedDate'),
            'ReturnScrappedProducts.ApproveStatus',
        )
        ->join('Product', 'Product.ProductCode', 'ReturnScrappedProducts.ProductCode');

        if ($roleId !== 'admin') {
            $returnScrapProducts->where('ReturnScrappedProducts.RequestedBy', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $returnScrapProducts->get(),
            ]);
        } else {
            return $returnScrapProducts->paginate($take);
        }
    }

    public function searchProduct($product)
    {
        $userId = Auth::user()->UserId;
        $allProduct = Product::select(
                        'Product.ProductCode as id',
                        DB::raw("CONCAT(Product.ProductCode,'-',Product.ProductName) AS ProductName"),
                           'DealarStock.CurrentStock as StockQty',
                             'Product.UnitPrice',
                             'Product.Vat',
                           'ReturnScrappedProducts.RequestToReturnQnty as alreadyReturnQnty',
                      )
            ->leftJoin('DealarStock', function ($join) use ($userId) {
                $join->on('DealarStock.ProductCode', '=', 'Product.ProductCode');
                $join->where('DealarStock.MasterCode', '=', $userId);
            })
            ->leftJoin('ReturnScrappedProducts', function ($join)  use ($userId) {
                $join->on('ReturnScrappedProducts.ProductCode', '=', 'Product.ProductCode');
                $join->where('ReturnScrappedProducts.RequestedBy', '=', $userId);
                $join->where('ReturnScrappedProducts.ApproveStatus','=', 'pending');
            })
            ->where(function ($q) use ($product) {
                $q->where('Product.ProductName', 'LIKE', '%' . $product . '%');
                $q->orWhere('Product.ProductCode', 'LIKE', '%' . $product . '%');
            })
            ->where('Product.Active', 'Y')
            ->where('Product.SMSOrder', 'Y')
            ->get();

        return response()->json([
            'allProduct' => $allProduct
        ]);
    }

    public function addScrapProduct( Request $request){
        try {
            DB::beginTransaction();
            $scrapProducts = new ReturnScrappedProducts();
            $scrapProducts->ProductCode = $request['product']['id'];
            $scrapProducts->RequestToReturnQnty = $request->requestToReturn;
            $scrapProducts->Reason = $request->reason;;
            $scrapProducts->ApproveStatus = 'Pending';
            $scrapProducts->UnitPrice = $request['product']['UnitPrice'];
            $scrapProducts->Vat = $request['product']['Vat'];
            $scrapProducts->Total = round((floatval($request['product']['UnitPrice']) + floatval($request['product']['Vat']))*$request->requestToReturn,4) ;
            $scrapProducts->RequestedBy = Auth::user()->UserId;
            $scrapProducts->RequestedDate = Carbon::now();
            $scrapProducts->save();

            DB::commit();
            return response()->json([
                'status' => 'Success',
                'message' => 'Scrap Product Added Successfully'
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function getExistingScrap($scrapId){
        try{
            $userId = Auth::user()->UserId;
            $existingScrapInfo = ReturnScrappedProducts::select(
                'ReturnScrappedProducts.ScrapID',
                'Product.ProductCode as id',
                DB::raw("CONCAT(Product.ProductCode,'-',Product.ProductName) AS ProductName"),
                'ReturnScrappedProducts.RequestToReturnQnty',
                'DealarStock.CurrentStock as StockQty',
                'ReturnScrappedProducts.Reason',
                'ReturnScrappedProducts.ApproveQnty',
                'ReturnScrappedProducts.UnitPrice',
                'ReturnScrappedProducts.Vat',
                'ReturnScrappedProducts.Total',
                'ReturnScrappedProducts.RequestedBy',
                DB::raw('convert(date,ReturnScrappedProducts.RequestedDate) as RequestedDate'),
                'ReturnScrappedProducts.ApprovedBy',
                DB::raw('convert(date,ReturnScrappedProducts.ApprovedDate) as ApprovedDate'),
                'ReturnScrappedProducts.ApproveStatus',
              )
                ->leftJoin('DealarStock', function ($join) use ($userId) {
                    $join->on('DealarStock.ProductCode', '=', 'ReturnScrappedProducts.ProductCode');
                    $join->where('DealarStock.MasterCode', '=', $userId);
                })
                ->join('Product', 'Product.ProductCode', 'ReturnScrappedProducts.ProductCode')
                ->where('ScrapID', $scrapId)->first();

            $alreadyRequested =  ReturnScrappedProducts::select( DB::raw("SUM(RequestToReturnQnty) as alreadyReturnQnty"),)
                ->groupBy('ProductCode')
                ->where('productCode',$existingScrapInfo->id)
                ->where('RequestedBy', $userId)
                ->where(function ($q) {
                    $q->where('ApproveStatus', 'Pending');
                    $q->orWhere('ApproveStatus', 'Reject');
                })
                ->get();

            return response()->json([
                'status' => 'Success',
                'existingScrapInfo' => $existingScrapInfo,
                'alreadyRequested' => $alreadyRequested
            ], 200);
        }
        catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function updateScrapProduct(Request $request){
        try {
            DB::beginTransaction();
            ReturnScrappedProducts::where('ScrapID', $request->scrapId)->update([
                'Reason' => $request->reason,
                'RequestToReturnQnty' => $request->requestToReturn,
            ]);
            DB::commit();
            return response()->json([
                'status' => 'Success',
                'message' => 'Scrap Product Updated Successfully'
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function getPendingScrapProducts(Request $request) {
        $status = $request->input('status');
        $export = $request->input('export');

        $currentPage = $request->input('pagination.current_page') ?? 1;
        $perPage = 20;

        // Check for valid status values
        if (!in_array($status, ['Y', 'N'])) {
            return response()->json(['error' => 'Invalid status parameter'], 400);
        }

        $query = DB::table('ReturnScrappedProducts')
            ->join('Product', 'Product.ProductCode', 'ReturnScrappedProducts.ProductCode')
            ->join('Customer', 'Customer.CustomerCode', 'ReturnScrappedProducts.RequestedBy')
            ->where('ReturnScrappedProducts.ApproveStatus', '=','Pending')
            ->select(
                'ReturnScrappedProducts.ScrapID',
                'Product.ProductCode as id',
                DB::raw("CONCAT(Product.ProductCode,'-',Product.ProductName) AS ProductName"),
                'ReturnScrappedProducts.RequestToReturnQnty',
                'ReturnScrappedProducts.Reason',
                'Customer.CustomerName',
                'ReturnScrappedProducts.ApproveQnty',
                'ReturnScrappedProducts.UnitPrice',
                'ReturnScrappedProducts.Vat',
                'ReturnScrappedProducts.Total',
                'ReturnScrappedProducts.RequestedBy',
                DB::raw('convert(date,ReturnScrappedProducts.RequestedDate) as RequestedDate'),
                'ReturnScrappedProducts.ApprovedBy',
                DB::raw('convert(date,ReturnScrappedProducts.ApprovedDate) as ApprovedDate'),
                'ReturnScrappedProducts.ApproveStatus'
            );

        if ($export != 'Y') {
            $dataList = $query->paginate($perPage, ['*'], 'page', $currentPage);
            $paginationData = [
                'current_page' => $dataList->currentPage(),
                'last_page' => $dataList->lastPage(),
                'total' => $dataList->total(),
                'from' => $dataList->firstItem(),
                'to' => $dataList->lastItem(),
            ];
            $dataList = json_decode(json_encode($dataList->items()), true);
        } else {
            $dataList = $query->get();
            $numberOfRecord = $dataList->count();
            $paginationData = [
                'current_page' => 1,
                'last_page' => 1,
                'total' => $numberOfRecord,
                'from' => 1,
                'to' => $numberOfRecord,
            ];
        }

        return response()->json([
            'data' => $dataList,
            'paginationData' => $paginationData
        ]);
    }

    public function approveScrapProducts(Request $request){
        $selectedItems = $request->selectedItems;
        DB::beginTransaction();
        try{
            foreach ($selectedItems as $item) {
                ReturnScrappedProducts::where('ScrapID', $item['ScrapID'])->update([
                    'ApproveQnty' => $item['approveQty'],
                    'ApproveStatus' =>intval($item['RequestToReturnQnty']) ===intval($item['approveQty'])? 'Approved' : 'Partial',
                    'ApprovedBy' =>  Auth::user()->UserId,
                    'ApprovedDate' => Carbon::now(),
                ]);
                DB::commit();
            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Scrap Product Approved Successfully'
            ], 200);


        }
        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function rejectScrapProducts(Request $request){
        $selectedItems = $request->selectedItems;

        try{
            DB::beginTransaction();
            foreach ($selectedItems as $item) {
                ReturnScrappedProducts::where('ScrapID', $item['ScrapID'])->update([
                    'ApproveStatus' =>'Rejected',
                    'ApprovedBy' =>  Auth::user()->UserId,
                    'ApprovedDate' => Carbon::now(),
                ]);
                DB::commit();
            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Scrap Product Rejected Successfully'
            ], 200);


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
