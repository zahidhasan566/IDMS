<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceDetails;
use App\Models\InvoiceDetailsBatch;
use App\Models\PreBookingRe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SapInvoiceController extends Controller
{
    public function storeSapInvoice(Request $request)
    {
        $dt = date('Y-m-d H-i-s A');
        try {
            $singleCustomer = $request->all();

            if (!empty($singleCustomer['InvoiceNo'])
                && !empty($singleCustomer['InvoicePeriod'])
                && !empty($singleCustomer['InvoiceDate'])
                && !empty($singleCustomer['Business'])
                && !empty($singleCustomer['OrderDate'])
                && !empty($singleCustomer['DeliveryDate'])
                && !empty($singleCustomer['DeliveryTime'])
                && !empty($singleCustomer['CustomerCode'])
                && !empty($singleCustomer['TP'])
                && !empty($singleCustomer['NET'])
                && !empty($singleCustomer['NSI'])
                && !empty($singleCustomer['Paid'])
            ) {
                DB::beginTransaction();
                //Customer Invoice Create
                $customer = new Invoice();
                $customer->InvoiceNo = $singleCustomer['InvoiceNo'];
                $customer->InvoicePeriod = $singleCustomer['InvoicePeriod'];
                $customer->Business = $singleCustomer['Business'];
                $customer->OrderDate = $singleCustomer['OrderDate'];
                $customer->DeliveryDate = $singleCustomer['DeliveryDate'];
                $customer->CustomerCode = $singleCustomer['CustomerCode'];
                $customer->TP = $singleCustomer['TP'];
                $customer->VAT = $singleCustomer['VAT'];
                $customer->Discount = $singleCustomer['Discount'];
                $customer->NET = $singleCustomer['NET'];
                $customer->NSI = $singleCustomer['NSI'];
                $customer->Paid = $singleCustomer['Paid'];
                $customer->PrepareBy =Auth::user()->UserId;
                $customer->PrepareDate =Carbon::now();
                $customer->IpAdress = $request->ip();
                $customer->save();

                foreach ($singleCustomer['InvoiceDetails'] as $key => $invoiceDetails) {
                    if(empty($invoiceDetails['ProductCode']) ||  empty($invoiceDetails['UnitPrice']) || empty($invoiceDetails['SalesTP'])  || empty($invoiceDetails['SalesQTY']) || empty($invoiceDetails['TP'])   ){
                        file_put_contents('public/log/sap_invoice/invoice_details_missing-' . $dt .'-'.$singleCustomer['InvoiceNo']. '.txt', json_encode($invoiceDetails) . "\n", FILE_APPEND);
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'Missing Invoice Details Parameter',
                            'InvoiceDetails' => $invoiceDetails
                        ], 422);
                    }
                    else{
                        $details = new InvoiceDetails();
                        $details->Invoiceno = $singleCustomer['InvoiceNo'];
                        $details->ProductCode = $invoiceDetails['ProductCode'];
                        $details->UnitPrice = $invoiceDetails['UnitPrice'];
                        $details->UnitVAT = $invoiceDetails['UnitVAT'];
                        $details->SalesTP = $invoiceDetails['SalesTP'];
                        $details->SalesQTY = $invoiceDetails['SalesQTY'];
                        $details->TP = $invoiceDetails['TP'];
                        $details->VAT = $invoiceDetails['VAT'];
                        $details->Discount = $invoiceDetails['Discount'];
                        $details->save();
                    }
                }

                foreach ($singleCustomer['InvoiceDetailsBatch'] as $key => $invoiceDetailsBatch) {
                    if(empty($invoiceDetailsBatch['BatchNo'])  && empty($invoiceDetailsBatch['Quantity'])  && empty($invoiceDetailsBatch['SalesQTY'])      ){
                        file_put_contents('public/log/sap_invoice/invoice_details_batch_missing-' . $dt .'-'.$singleCustomer['InvoiceNo']. '.txt', json_encode($invoiceDetailsBatch) . "\n", FILE_APPEND);
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'Missing Invoice Details Batch Parameter',
                            'InvoiceDetails' => $invoiceDetailsBatch
                        ], 422);
                    }
                    else{
                        $detailsBatch = new InvoiceDetailsBatch();
                        $detailsBatch->Invoiceno = $singleCustomer['InvoiceNo'];
                        $detailsBatch->ProductCode = $invoiceDetails['ProductCode'];
                        $detailsBatch->BatchNo = $invoiceDetailsBatch['BatchNo'];
                        $detailsBatch->Quantity = $invoiceDetailsBatch['Quantity'];
                        $detailsBatch->SalesQTY = $invoiceDetailsBatch['SalesQTY'];
                        $detailsBatch->save();
                    }
                }

                DB::commit();

                file_put_contents('public/log/sap_invoice/sap_invoice_success-' . $dt . '.txt', json_encode($singleCustomer) . "\n", FILE_APPEND);
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Invoice Added Successfully',
                    'InvoiceNo' => $customer->InvoiceNo
                ], 200);


            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Missing Required Parameter',
                    'Customer' => $singleCustomer
                ], 422);
            }
        }
        catch (\Exception $exception) {
            DB::rollBack();
            file_put_contents('public/log/sap_invoice/sap_invoice_error-' . $dt . '.txt', $exception->getMessage() . '-' . $exception->getLine() . "\n", FILE_APPEND);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }
}
