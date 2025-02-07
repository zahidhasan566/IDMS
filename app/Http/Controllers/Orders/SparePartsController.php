<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\Orders\OrderSparepartsCollection;
use App\Models\Logistics\DealerDocument;
use App\Models\OrderInvoiceDetails;
use App\Models\OrderInvoiceMaster;
use App\Models\Product;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SparePartsController extends Controller
{
    use CommonTrait;
    public function index(Request $request)
    {
        $search = $request->get('query');
        $prevsixmonth = date("Y-m-d", strtotime("-6 months"));
        $today = date("Y-m-d");
        $order = DB::table('OrderInvoiceDetails AS od')
            ->select('od.OrderNo', 'od.ProductCode', 'p.ProductName', DB::raw('CONVERT(int,od.Quantity) as Quantity'),
            DB::raw('CONVERT(int,od.UnitPrice) as UnitPrice'), DB::raw('CONVERT(int,od.VAT) as VAT'),
            'u.UserName', DB::raw('CONVERT(date,m.OrderDate) as OrderDate'),
                DB::raw('CONVERT(INT,((od.UnitPrice +od.Vat) * od.Quantity)) as TotalPrice'),
                DB::raw("Case when m.Level1Approved ='Y' then 'Yes' when m.Level1Approved='N' then 'No' when m.Level1Approved='C' then 'Cancel' END Level1Approved"),
                DB::raw("Case when m.Level2Approved ='Y' then 'Yes' when m.Level2Approved='N' then 'No' when m.Level2Approved='C' then 'Cancel' END Level2Approved"),
                DB::raw("Case when m.Level3Approved ='Y' then 'Yes' when m.Level3Approved='N' then 'No' when m.Level3Approved='C' then 'Cancel' END Level3Approved"))
            ->join('OrderInvoiceMaster as m ', 'm.OrderNo', 'od.OrderNo')
            ->join('UserManager as u', 'u.UserID', 'm.MasterCode')
            ->join('Product as p', 'p.ProductCode', 'od.ProductCode')
            ->where('p.Business', '=', 'P')
            ->where('m.MasterCode', Auth::user()->UserId)
            ->wherebetween('m.OrderDate',[$prevsixmonth,$today]);
        if (!empty($search)) {
            $order->where(function ($q) use ($search) {
                $q->where('u.UserName', 'LIKE', '%' . $search . '%');
                $q->orWhere('od.ProductCode', 'LIKE', '%' . $search . '%');
            });
        }
        return response()->json([
            'data' => $order->orderBy('m.OrderDate', 'desc')->paginate(15)
        ]);
    }

    public function storeSparePartsOrder(Request $request ){
        try {

            DB::beginTransaction();
            $products = $request->products;
            foreach ($products as $key => $value){
                if(empty($value['ProductCode'])){
                    unset($products[$key]);
                }
            }
            $products = array_values($products);
            $preparedArray = $products;
            $unique_check = collect($preparedArray);
            $unique_check = $unique_check->pluck('ProductCode');

            $productCodes = [];
            foreach ($unique_check as $each) {
                $productCodes[] = $each;
            }
            $unique = array_unique($productCodes);

            $unique_check = $unique_check->toArray();

            $result = array_values(array_diff_key($unique_check, $unique));

            if ($result) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have added '.$result[0].' multiple times!'
                ]);
            }
            $bike = new OrderInvoiceMaster();
            $bike->MasterCode =  Auth::user()->UserId;
            $bike->OrderDate = Carbon::now()->format("Y-m-d 00:00:00.000");
            $bike->OrderTime =   Carbon::now();
            $bike->InvoiceOK =   'N';

            $bike->SendTime =    Carbon::now();
            $bike->IPAddress =  $request->ip() ;

            if ($bike->save()){
                foreach ($preparedArray as $key => $value){
                    if (  $value['Quantity'] >0 ){
                        $details = new OrderInvoiceDetails();
                        $details->OrderNo = $bike->OrderNo;
                        $details->ProductCode = $value['ProductCode'];
                        $details->Quantity = $value['Quantity'];
                        $details->UnitPrice = $value['UnitPrice'];
                        $details->Vat = $value['Vat'];
                        $details->save();
                    }else{
                        return response()->json([
                            'status' => 'error',
                            'message'=>$value['ProductCode'].' Quantity Cannot Be Zero.'
                        ]);
                    }
                }
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message'=>'Order has been placed'
                ]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function getExcelFile()
    {
        $userId = Auth::user()->UserId;
        $exportsep= Product::select('Product.*','DealarStock.CurrentStock')->leftjoin('DealarStock',function ($q) use ($userId){
                $q->on('DealarStock.ProductCode', 'Product.ProductCode');
                $q->where('DealarStock.MasterCode', $userId);
            })
            ->where('Product.Business','P')
            ->where('Product.Active','Y')
            ->where('Product.MRP','>','0')
            ->where('Product.UnitPrice','>','0')
            ->where('Product.SMSOrder','Y')->get();
        return new OrderSparepartsCollection($exportsep);
    }
//    public function FuncFilterProductData ($val){
//        global  $filterProductCode;
//        if($val['ProductCode'] == $filterProductCode){
//            return true;
//        }
//    }

    public function importExcelFile(Request $request){
        $data = [];
        $allParts =$this->stockProductList();
        try {
            $ProductList = $request->products;
            foreach ($ProductList as $key => $singleData){

                if(!empty($singleData['Quantity']))
                {
                    global $filterProductCode;
                    $filterProductCode  = $singleData['Product Code'];
                    $filterProduct =array_values( array_filter($allParts, function ($item) use ($allParts) {
                        global  $filterProductCode;
                        if($item['ProductCode'] == $filterProductCode){
                            return true;
                        }
                    }));
                    if (!empty($filterProduct[0]['ProductName'])){
                        $product =[];
                        $product['PartName']  =  $singleData['Product Code'] .' - '.$filterProduct[0]['ProductName'];
                        $product['ProductCode']  =  $filterProduct[0]['ProductCode'];
                        $product['PartsCode']  =   $filterProduct[0]['PartNo'];
                        $product['ProductName']  =  $filterProduct[0]['ProductName'];
                        $product['Vat']  = $filterProduct[0]['VAT'];
                        $product['UnitPrice']  = $filterProduct[0]['UnitPrice'];
                        $product['Quantity']  =  $singleData['Quantity'];
                        $product['CurrentStock']  =  $singleData['Current Stock'];
                        $product['TotalPrice']  =  number_format(($filterProduct[0]['UnitPrice']+$filterProduct[0]['VAT']) *(!empty($singleData['Quantity'])? $singleData['Quantity']:0),2);
                        $product['importStatus']  =  true;
                        array_push($data,$product);
                    }


                }

            }

        }
        catch (\Exception $exception) {
            return $exception->getMessage();
        }


        return response()->json([
            'products'=>$data,
        ]);
    }
}
