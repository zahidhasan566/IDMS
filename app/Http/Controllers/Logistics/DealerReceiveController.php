<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DealearInvoiceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealerReceiveController extends Controller
{
    public function getAllPendingDocument(Request $request){
        $userId =Auth::user()->UserId;
      $list = DB::table('DealearInvoiceDocument as d')->select(
          'd.ReceiveId', 'd.InvoiceNo',
          DB::raw("CONCAT(d.CustomerCode,': ',c.CustomerName )as Customer , CONCAT(d.ProductCode,': ',p.ProductName )as Product") ,
          'd.ChassisNo','d.EngineNo', 'd.SendDate')
          ->join('Product as p','p.ProductCode','=','d.ProductCode')
          ->join('Customer as c','c.CustomerCode','=','d.CustomerCode')
          ->where('d.IsReceive','=',null)
          ->where('d.CustomerCode', '=',$userId)
          ->orderby('d.ReceiveId', 'desc')->get();
        return response()->json([
            'data'=>$list,
            'status' => 'success',
        ]);
    }

    public function updateLogisticsDocument(Request $request){
        $allData =$request->allData;
        foreach ($allData as $key){
            $ReceiveId = $key["ReceiveId"];
            $document = DealearInvoiceDocument::where('ReceiveId','=',$ReceiveId)->first();
            $document->IsReceive ='Y';
            $document->ReceiveDate =$key["allReceiveDate"];
            $document->ReceiveIpAddress =\request()->ip();
            $document->ReceiveBy =Auth::user()->UserId;
            $document->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully Updated!',
        ]);

    }
}
