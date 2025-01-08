<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InboundController extends Controller
{
    //Order Invoice
    public function getBikeOrder(Request $request){
        $dateFrom =$request->dateFrom;
        $dateTo = $request->dateTo . ' 23:59:59';
        $validator = Validator::make($request->all(), [
            'dateFrom' => 'required',
            'dateTo' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }


        $bikeOrder = DB::table('OrderInvoiceMaster as om')
            ->select('om.OrderNo','om.MasterCode as CustomerCode',DB::raw('Convert(date,om.OrderDate) as OrderDate'),
            'u.UserName','d.ProductCode','p.ProductName','d.Quantity', 'd.UnitPrice',
            'd.Vat',DB::raw('((d.UnitPrice +d.Vat) * d.Quantity) as TotalPrice'),
            DB::raw("Case when om.Level1Approved ='Y' then 'Yes' when om.Level1Approved='N' then 'No' when om.Level1Approved='C' then 'Cancel' END Level1Approved"),
            DB::raw("Case when om.Level2Approved ='Y' then 'Yes' when om.Level2Approved='N' then 'No' when om.Level2Approved='C' then 'Cancel' END Level2Approved"),
            DB::raw("Case when om.Level3Approved ='Y' then 'Yes' when om.Level3Approved='N' then 'No' when om.Level3Approved='C' then 'Cancel' END Level3Approved"))
            ->join('OrderInvoiceDetails as d ','d.OrderNo','=','om.OrderNo')
            ->join('UserManager as u','u.UserID','=','om.MasterCode')
            ->join('Product as p','p.ProductCode','=','d.ProductCode')
            ->where('p.Business','=','C')
            ->where('om.Level2Approved','=','Y')
            ->wherebetween('OrderDate',[$dateFrom,$dateTo]);

        return response()->json([
            'bikeOrder'=>$bikeOrder->orderBy('OrderDate','Desc')->get()
        ]);
    }

    public function getSparePartsOrder(Request $request){
        $dateFrom =$request->dateFrom;
        $dateTo = $request->dateTo . ' 23:59:59';
        $validator = Validator::make($request->all(), [
            'dateFrom' => 'required',
            'dateTo' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        $sparePartsOrder = DB::table('OrderInvoiceMaster as om')
            ->select('om.OrderNo','om.MasterCode as CustomerCode',DB::raw('Convert(date,om.OrderDate) as OrderDate'),
                'u.UserName','d.ProductCode','p.ProductName','d.Quantity', 'd.UnitPrice',
                'd.Vat',DB::raw('((d.UnitPrice +d.Vat) * d.Quantity) as TotalPrice'),
                DB::raw("Case when om.Level1Approved ='Y' then 'Yes' when om.Level1Approved='N' then 'No' when om.Level1Approved='C' then 'Cancel' END Level1Approved"),
                DB::raw("Case when om.Level2Approved ='Y' then 'Yes' when om.Level2Approved='N' then 'No' when om.Level2Approved='C' then 'Cancel' END Level2Approved"),
                DB::raw("Case when om.Level3Approved ='Y' then 'Yes' when om.Level3Approved='N' then 'No' when om.Level3Approved='C' then 'Cancel' END Level3Approved"))
            ->join('OrderInvoiceDetails as d ','d.OrderNo','=','om.OrderNo')
            ->join('UserManager as u','u.UserID','=','om.MasterCode')
            ->join('Product as p','p.ProductCode','=','d.ProductCode')
            ->where('p.Business','=','P')
            ->where('om.Level2Approved','=','Y')
            ->wherebetween('OrderDate',[$dateFrom,$dateTo]);

        return response()->json([
            'sparePartsOrder'=>$sparePartsOrder->orderBy('OrderDate','Desc')->get()
        ]);
    }

    public function getPaymentInfo(Request $request){
        $dateFrom =$request->dateFrom;
        $dateTo = $request->dateTo . ' 23:59:59';
        $business = 'C';
        $validator = Validator::make($request->all(), [
            'dateFrom' => 'required',
            'dateTo' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }


        $payment = DB::table('PaymentTempOnline as PTO')->Select(
            'PTO.CustomerCode as DealerCode',
            'PTO.MoneyRecNoTemp',
            'PTO.Approved',
            'd.DepotName',
            'bs.BusinessName',
            DB::raw("convert(date,PTO.PaymentDate) as PaymentDate "),
            'PTO.PaymentAmount',
            'PTO.SalesType',
            'c.CustomerName',
            'PTO.PaymentMode',
            'PTO.Reference',
            'PTO.ChequeNo',
            'b.BankName',
            DB::raw("convert(date,PTO.ChequeDate) as ChequeDate "),
            'PTO.CheqBranch',
            DB::raw("convert(date,PTO.PreparedDate) as PreparedDate "),
            'PTO.ChequeImage',
        )
            ->join('Banks as b','b.BankCode','PTO.BankCode')
            ->join('Business as bs','bs.Business','PTO.Business')
            ->leftJoin('Depot as d','d.DepotCode','PTO.DepotCode')
            ->leftjoin('Customer as c','c.CustomerCode','PTO.CustomerCode')
            ->where('PTO.Business',$business)
            ->whereNotNull('PTO.ChequeImage')
            ->wherebetween('PTO.PaymentDate',[$dateFrom, $dateTo]);

        return response()->json([
            'payment'=>$payment->get()
        ]);

    }

}
