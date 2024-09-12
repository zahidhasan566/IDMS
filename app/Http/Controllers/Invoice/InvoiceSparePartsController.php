<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Services\AffiliatedAgentService;
use App\Services\Invoice\InvoiceSparePartsService;
use App\Services\MechanicsService;
use App\Services\StockService;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceSparePartsController extends Controller
{
    use CodeGeneration,CommonTrait;

    public function index(Request $request)
    {
        $take = $request->take;
        $startDate = Carbon::now()->subDay(7)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        $user = Auth::user();
        $query = DB::table('DealarInvoiceMaster')->leftJoin('DealarInvoiceDetails','DealarInvoiceDetails.InvoiceID','DealarInvoiceMaster.InvoiceID')
            ->where('DealarInvoiceDetails.ChassisNo','')
            ->whereBetween('InvoiceDate',[$startDate,$endDate]);
        if ($user->RoleId === 'customer') {
            $query->where('DealarInvoiceMaster.MasterCode',$user->UserId);
        }
        $invoices = $query->select('DealarInvoiceMaster.InvoiceID','DealarInvoiceMaster.InvoiceNo','InvoiceTime','CustomerName','MobileNo as CustomerMobile',
            DB::raw("SUM(DealarInvoiceDetails.Quantity) as Quantity"),
            DB::raw("SUM(DealarInvoiceDetails.UnitPrice) as TotalUnitPrice"),
            DB::raw("SUM(DealarInvoiceDetails.VAT) as TotalVAT"),
            DB::raw("SUM(DealarInvoiceDetails.Discount) as TotalDiscountPercentage"),
            DB::raw("SUM(((DealarInvoiceDetails.UnitPrice + DealarInvoiceDetails.VAT) * DealarInvoiceDetails.Quantity) - ((DealarInvoiceDetails.UnitPrice + DealarInvoiceDetails.VAT) * DealarInvoiceDetails.Quantity) * (1/100)) as TotalPrice"))
            ->groupBy('DealarInvoiceMaster.InvoiceID','DealarInvoiceMaster.InvoiceNo','InvoiceTime','CustomerName','MobileNo')
            ->orderBy('InvoiceTime','desc');

        //dd($invoices->toSql());
        return $invoices->paginate($take);


    }

    public function getSparePartsSupportingData()
    {
        $userId = Auth::user()->UserId;
        $mechanicsService = new MechanicsService($userId,'');
        $affiliatorService = new AffiliatedAgentService('');
        return response()->json([
            'status' => 'success',
            'mechanics' => $mechanicsService->getList(),
            'affiliators' => $affiliatorService->getList()
        ]);
    }

    public function filterSpareParts(Request $request)
    {
        $userId = Auth::user()->UserId;
        $business = 'P';
        $productCode = $request->search;
        $result = DB::select("EXEC usp_LoadProduct '$business','$productCode','0','$userId'");
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    public function stockCheck(Request $request)
    {
        $request->validate([
            'productCode' => 'required'
        ]);
        $userId = Auth::user()->UserId;
        $productCode = $request->productCode;
        $stockService = new StockService($userId,$productCode);
        return response()->json([
            'status' => 'success',
            'stock' => $stockService->check() == null ? 0 : $stockService->check()
        ]);
    }

    public function createInvoice(Request $request)
    {
        $request->validate([
            'customerName' => 'required',
            'fields' => 'required'
        ]);
        $userId = Auth::user()->UserId;
        $invoiceDate = date('Y-m-d', strtotime("now"));
        $invoiceTime = date('Y-m-d H:i:s', strtotime("now"));
        $customerName = $request->customerName;
        $customerAddress = $request->customerAddress;
        $customerMobile = $request->customerMobile;
        $affiliator = $request->affiliator;
        $mechanics = $request->reference;
        $affiliatorCode = '';
        $affiliatorMobile = '';
        $fields = $request->fields;
        if(!empty($affiliator)) {
            $affiliator = explode("###",$affiliator);
            $affiliatorCode = $affiliator[0];
            $affiliatorMobile = $affiliator[1];
        }
        $affiliatorDiscount = $request->affiliatorDiscount;
        $ipAddress = $request->ip();
        //CREATE INVOICE NO
        $invoiceNo = $this->generateJobCardInvoiceNo();
        if ($invoiceId = InvoiceSparePartsService::createInvoice($userId, $invoiceDate, $invoiceTime, $customerName, $customerAddress, $customerMobile,
            $ipAddress, $fields, $mechanics, $affiliatorCode, $affiliatorDiscount,$invoiceNo)) {
            $affiliatorSMSText = "Spare Parts has been sold using your Affiliator Reference \n";
            $affiliatorSMSText .= "Code: ".$affiliatorCode.", Customer Name:  ".$customerName.", Discount: ".$affiliatorDiscount;
            $affiliatorSMSText .=", InvoiceID: ".$invoiceNo." at ".$invoiceTime;
            //$this->sendSms($affiliatorMobile,$affiliatorSMSText);
            return response()->json([
                'status' => 'success',
                'message' => 'Invoice created successfully.',
                'invoiceId' => $invoiceId
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }

    public function getInvoiceData(Request $request)
    {
        $request->validate([
            'invoiceNo' => 'required'
        ]);
        try {
            $invoiceNo = $request->invoiceNo;
            $userId = Auth::user()->UserId;
            return response()->json(InvoiceSparePartsService::getInvoiceDetails($invoiceNo,$userId));
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }

    public function returnInvoice(Request $request)
    {
        $request->validate([
            'invoiceNo' => 'required',
            'invoiceDetails' => 'required'
        ]);
        $error = 0;
        if (count($request->invoiceDetails)) {
            foreach ($request->invoiceDetails as $detail) {
                if (intval($detail['rQuantity']) > 0) {
                    if (!InvoiceSparePartsService::return($detail,$request->invoiceNo)) {
                        $error += 1;
                    }
                }
            }
        }
        if ($error > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Something went wrong!'
            ],500);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice has been returned successfully'
            ]);
        }
    }

    public function sparePartsInvoicePrint($invoiceId)
    {
        $invoice = DealarInvoiceMaster::leftJoin('Customer','Customer.CustomerCode','DealarInvoiceMaster.MasterCode')
            ->where('DealarInvoiceMaster.InvoiceID',$invoiceId)
            ->select('DealarInvoiceMaster.InvoiceID','DealarInvoiceMaster.InvoiceNo','DealarInvoiceMaster.InvoiceDate','DealarInvoiceMaster.InvoiceTime',
                'DealarInvoiceMaster.CustomerName','DealarInvoiceMaster.PreAddress','DealarInvoiceMaster.MobileNo',
                'Customer.CustomerCode as DealerCode','Customer.CustomerName as DealerName','Customer.Add1','Customer.Add2','Customer.Mobile',
                'AffiliatorCode','AffiliatorDiscount')
            ->with('partsDetails')
            ->first();
        if ($invoice) {
            return response()->json([
                'data' => $invoice
            ]);
        }
        return response()->json([
            'message' => 'No such invoice!'
        ],404);
    }
}
