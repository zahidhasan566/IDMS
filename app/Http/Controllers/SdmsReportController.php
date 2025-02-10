<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SdmsReportController extends Controller
{
    public function invoiceList(Request $request)
    {
        $take = $request->take;
        $userId = $request->customer;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $query = Invoice::join('Business as B', 'B.Business', 'Invoice.Business')
            ->join('Depot as D', 'D.DepotCode', 'Invoice.DepotCode')
            ->join('Customer as C', 'C.CustomerCode', 'Invoice.CustomerCode')
            ->whereBetween('InvoiceDate', [$startDate, $endDate]);
        $query->where('C.CustomerCode', $userId);

        return $query->select('B.BusinessName', 'Invoice.InvoiceNo', DB::raw("CONVERT(date,Invoice.InvoiceDate) as InvoiceDate"), 'D.DepotName', 'C.CustomerCode', 'C.CustomerName')
            ->paginate($take);
    }

    public function invoiceDetails($invoiceNo)
    {
        $sql = "SELECT C.CustomerCode, CT.CustTypeName, C.CustomerName, C.ContactPerson,C.Add1 + ' ' + C.Add2 Address , c.Mobile, c.Phone,
                    I.InvoiceDate, i.CISSNo, I.DeliveryDate, L.Level1Name, T.TTYName, I.PODate, I.PONumber, I.Reference,
                    P.ProductName, ID.SalesQTY + ID.BonusQTY Quantity, ID.SalesTP + ID.SalesVat DP, ID.Discount, ID.NET,
                    IDb.BatchNo ChassisNo, sb.engineno
                    FROM Invoice I
                        INNER JOIN InvoiceDetails ID
                            ON I.InvoiceNo  = ID.Invoiceno
                        INNER JOIN InvoiceDetailsBatch IDB
                            ON ID.Invoiceno = IDB.Invoiceno AND ID.ProductCode = IDB.ProductCode
                        INNER JOIN StockBatch sb 
                            ON idb.BatchNo = sb.BatchNo AND IDB.ProductCode = sb.ProductCode
                        INNER JOIN Product P
                            ON ID.ProductCode = P.ProductCode
                        INNER JOIN Customer C
                            ON I.CustomerCode = C.CustomerCode
                        INNER JOIN Territory T
                            ON T.TTYCode = C.TTYCode
                        INNER JOIN CustomerType CT
                            ON C.CustomerType = CT.CustomerType
                        INNER JOIN Level1 L
                            ON I.Level1 = L.Level1
                    WHERE I.InvoiceNo = '$invoiceNo'
                    ORDER BY ID.NET DESC";
        $data = DB::select($sql);
        return response()->json([
            'data' => $data,
            'invoiceNo' => $invoiceNo
        ]);
    }

    public function doLoadSdmsCustomer($customercode)
    {
        $sql = "SELECT C.*,	CM.CustomerCode CustomerCodeSP, CC.PaymentMode PaymentModeSP
                    FROM Customer C 
                        LEFT JOIN CustomerMapping CM
                            ON C.CustomerCode = CM.CustomerMasterCode
                            AND CM.Business = 'P'
                        LEFT JOIN Customer CC
                            ON CC.CustomerCode = CM.CustomerCode
                    WHERE C.CustomerCode = '$customercode'";

        return DB::select($sql);
    }

    public function customerLedger(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $customerCode = $request->customerCode;
        //return $customerCode;
        $business = $request->business;
        $customer = $this->doLoadSdmsCustomer($customerCode);
        if (!empty($customer)) {
            $paymentMode = $business === 'P' ? $customer[0]->PaymentModeSP : $customer[0]->PaymentMode; //!empty($customer[0]->PaymentMode) ? $customer[0]->PaymentMode : '';
            $depotCode = !empty($customer[0]->DepotCode) ? $customer[0]->DepotCode : '';
            $customerCode = $business === 'P' ? $customer[0]->CustomerCodeSP : $customerCode;
            $sql = "exec sp_CustomerLedgerNew '$startDate','$endDate','$customerCode','$paymentMode','$depotCode'";
            $data = $this->getPdoResult($sql);
            $dataSet['customer_ledger_opening'] = $data[0];
            $dataSet['customer_ledger_details'] = $data[1];
            $dataSet['customer_ledger_closing'] = $data[2];
            $dataSet['masterInfo'] = [
                'customerName' => !empty($customer[0]->CustomerName) ? $customer[0]->CustomerName : '',
                'contactPerson' => !empty($customer[0]->ContactPerson) ? $customer[0]->ContactPerson : '',
                'customerCode' => $customerCode,
                'paymentMode' => $business === 'P' ? $customer[0]->PaymentModeSP : $customer[0]->PaymentMode,
                'address1' => !empty($customer[0]->Add1) ? $customer[0]->Add1 : '',
                'address2' => !empty($customer[0]->Add2) ? $customer[0]->Add2 : '',
                'mobile' => !empty($customer[0]->Phone) ? $customer[0]->Phone : '',
                'business' => $business,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'opening' => !empty($dataSet['customer_ledger_opening'][0]['Opening']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Opening']) . ')' : $dataSet['customer_ledger_opening'][0]['Opening'],
                'openingRaw' => $dataSet['customer_ledger_opening'][0]['Opening'],
                'dueAmount' => ($dataSet['customer_ledger_opening'][0]['DueAmount'] < 0) ? '(' . abs($dataSet['customer_ledger_opening'][0]['DueAmount']) . ')' : $dataSet['customer_ledger_opening'][0]['DueAmount'],
                'dueDays' => !empty($dataSet['customer_ledger_opening'][0]['DueDays']) ? $dataSet['customer_ledger_opening'][0]['DueDays'] : 0,
                'invoice' => !empty($dataSet['customer_ledger_opening'][0]['Invoice']) ? $dataSet['customer_ledger_opening'][0]['Invoice'] : '',
                'payment' => !empty($dataSet['customer_ledger_opening'][0]['Payment']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Payment']) . ')' : 0,
                'adjustment' => !empty($dataSet['customer_ledger_opening'][0]['Adjustment']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Adjustment']) . ')' : 0,
                'invoiceFooter' => $dataSet['customer_ledger_closing'][0]['Invoice'],
                'paymentFooter' => $dataSet['customer_ledger_closing'][0]['Payment'],
                'adjustmentFooter' => $dataSet['customer_ledger_closing'][0]['Adjustment'],
                'invoiceGrand' => $dataSet['customer_ledger_closing'][0]['InvoiceTotal'],
                'paymentGrand' => $dataSet['customer_ledger_closing'][0]['PaymentTotal'],
                'adjustmentGrand' => $dataSet['customer_ledger_closing'][0]['AdjustmentTotal']
            ];
            return response()->json($dataSet);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No Customer Found!'
            ], 500);
        }
    }

    public function customerWiseProductSold(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $customerCode = $request->customerCode;
        $customer = $this->doLoadSdmsCustomer($customerCode);
        if (!empty($customer)) {
            $business = $customer[0]->Business;
            $sql = "SELECT 
                           B.BusinessName, C.CustomerCode, C.CustomerName, D.DepotName,
                           P.ProductCode,P.ProductName, SUM(ID.SalesQTY) SalesQTY, SUM(ID.BonusQTY) AS BonusQTY,  SUM(ID.SalesQTY + ID.BonusQTY) TotalQnty,
                           ID.Discount, ID.SalesTP,  SUM(ID.SalesTP * ID.SalesQTY) Gross, SUM(SalesVat * ID.SalesQTY) VAT
                    FROM Invoice I
                           INNER JOIN Customer C
                                  ON I.CustomerCode= C.CustomerCode
                           INNER JOIN Depot D
                                  ON C.DepotCode = D.DepotCode
                           INNER JOIN Business B
                                  ON I.Business = B.Business
                           INNER JOIN InvoiceDetails ID
                                  ON I.InvoiceNo = ID.Invoiceno
                           INNER JOIN Product P
                                  ON P.ProductCode = ID.ProductCode
                    WHERE InvoiceDate BETWEEN '$startDate' AND '$endDate'
                    AND C.CustomerCode = '$customerCode'
                    AND I.Business  = '$business'
                    AND I.Returned = 'N'
                    GROUP BY B.BusinessName, C.CustomerCode, C.CustomerName, D.DepotName,
                           P.ProductCode,P.ProductName, ID.Discount, ID.SalesTP
                    ORDER BY ProductName
                    ";
            $data = DB::select($sql);
            return response()->json([
                'data' => $data,
                'masterData' => [
                    'business' => $data[0]->BusinessName,
                    'customerCode' => $customer[0]->CustomerCode,
                    'customerName' => $customer[0]->CustomerName,
                    'depotName' => $data[0]->DepotName
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No Customer Found!'
            ], 500);
        }
    }

    public function dayWiseSalesSummary(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $customerCode = $request->customerCode;
        $customer = $this->doLoadSdmsCustomer($customerCode);
        if (!empty($customer)) {
            $sql = "EXEC usp_doLoadNationalDayWiseSummery '$startDate','$endDate','$customerCode'";
            $data = DB::select($sql);
            return response()->json([
                'data' => $data,
                'masterInfo' => [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'customerCode' => $customerCode
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No Customer Found!'
            ], 500);
        }
    }

    public function getPdoResult($sql)
    {
        $conn = DB::connection('sqlsrv');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            array_push($res, $rows);
        } while ($pdo->nextRowset());
        return $res;
    }

    public function dealerOfferList(Request $request)
    {
        $request->validate([
            'startDate' => 'required'
        ]);
        $startDate = Carbon::parse($request->startDate)->format('Ym');
        $customerCode = $request->customer;
        $sql = "EXEC usp_doLoadDealerOfferReport '$startDate','$customerCode'";
        return response()->json([
            'data' => DB::select($sql)
        ]);
    }

    public function uploadOfferList(Request $request)
    {
        $request->validate([
            'startDate' => 'required',
            'dataSet' => 'required'
        ]);
        if (count($request->dataSet)) {
            try {
                $month = Carbon::parse($request->startDate)->format('m');
                $month = intval($month);
                $data = DB::table('DealerOffer')->whereRaw("MONTH(Month) = '$month'")->get();
                if (count($data)) {
                    DB::table('DealerOffer')->whereRaw("MONTH(Month) = '$month'")->delete();
                }
                foreach ($request->dataSet as $data) {
                    DB::table('DealerOffer')->insert([
                        'Code' => $data['Code'],
                        'Dealer' => $data['Dealer'],
                        'Particulars' => $data['Particulars'],
                        'Amount' => $data['Amount ']
                    ]);
                }
                return response()->json([
                    'message' => 'File uploaded successfully.'
                ]);
            } catch (\Exception $exception) {
                return response()->json([
                    'message' => 'Something went wrong!'
                ],500);
            }
        }
        return response()->json([
            'message' => 'No data posted!'
        ],500);
    }
}
