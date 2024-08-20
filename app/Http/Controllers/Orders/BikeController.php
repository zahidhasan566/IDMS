<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;

use App\Models\OrderInvoiceDetails;
use App\Models\OrderInvoiceMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BikeController extends Controller
{
    public function index(Request $request){
        $search = $request->get('query');
        $prevsixmonth = date("Y-m-d", strtotime("-6 months"));
        $today = date("Y-m-d");

        $order = DB::table('OrderInvoiceMaster as om')->select('om.OrderNo',DB::raw('Convert(date,om.OrderDate) as OrderDate'),
            'u.UserName','d.ProductCode','p.ProductName','d.Quantity', 'd.UnitPrice',
            'd.Vat',DB::raw('((d.UnitPrice +d.Vat) * d.Quantity) as TotalPrice'))
            ->join('OrderInvoiceDetails as d ','d.OrderNo','=','om.OrderNo')
            ->join('UserManager as u','u.UserID','=','om.MasterCode')
            ->join('Product as p','p.ProductCode','=','d.ProductCode')
            ->where('p.Business','=','C')
            ->where('om.MasterCode',Auth::user()->UserId)
            ->wherebetween('OrderDate',[$prevsixmonth,$today]);
            if(!empty($search)){
                $order->where(function ($q) use ($search){
                    $q->where('u.UserName', 'LIKE', '%'. $search . '%');
                    $q->orWhere('d.ProductCode', 'LIKE','%'.  $search . '%');
                });
            }
        return response()->json([
           'data'=>$order->orderBy('OrderDate','Desc')->paginate(15)
        ]);
    }
   public function storeBikeOrder(Request $request ){
       try {

           DB::beginTransaction();
           $preparedArray = $request->products;

           $unique_check = collect($preparedArray);
           $unique_check = $unique_check->pluck('ProductCode');

           $productCodes = [];
           foreach ($unique_check as $each) {
               $productCodes[] = $each['ProductCode'];
           }

           $unique = array_unique($productCodes);

           $unique_check = $unique_check->toArray();

           $result = array_diff_key($unique_check, $unique);

           if ($result) {
               return response()->json([
                   'status' => 'error',
                   'message' => 'You have added '.$result[1].' multiple times!'
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
                 if ( $value['Quantity'] >0){
                     $details = new OrderInvoiceDetails();
                     $details->OrderNo = $bike->OrderNo;
                     $details->ProductCode = $value['ProductCode']['ProductCode'];
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


}
