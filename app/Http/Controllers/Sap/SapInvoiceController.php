<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\DealarInvoicePayment;
use App\Models\FlagshipInvoiceBRTA;
use App\Models\Invoice;
use App\Models\InvoiceDetails;
use App\Models\InvoiceDetailsBatch;
use App\Models\JobCard\DealarInvoiceDetails;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Models\PreBookingRe;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

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
                && !empty($singleCustomer['Paid'])
            ) {
                DB::beginTransaction();
                //Customer Invoice Create
                $customer = new Invoice();
                $customer->InvoiceNo = $singleCustomer['InvoiceNo'];
                $customer->InvoicePeriod = $singleCustomer['InvoicePeriod'];
                $customer->InvoiceDate = $singleCustomer['InvoiceDate'];
                $customer->Business = $singleCustomer['Business'];
                $customer->OrderDate = $singleCustomer['OrderDate'];
                $customer->DeliveryDate = $singleCustomer['DeliveryDate'];
                $customer->CustomerCode = $singleCustomer['CustomerCode'];
                $customer->TP = $singleCustomer['TP'];
                $customer->VAT = $singleCustomer['VAT'];
                $customer->TAX = $singleCustomer['TAX'];
                $customer->Discount = $singleCustomer['Discount'];
                $customer->NET = $singleCustomer['NET'];
                $customer->NSI = $singleCustomer['NSI'];
                $customer->Paid = $singleCustomer['Paid'];
                $customer->VehicleNumber = $singleCustomer['VehicleNumber'];
                $customer->DriverName = $singleCustomer['DriverName'];
                $customer->DriverMobile = $singleCustomer['DriverMobile'];
                $customer->PrepareBy =Auth::user()->UserId;
                $customer->PrepareDate =Carbon::now();
                $customer->IpAdress = $request->ip();
                $customer->save();

                foreach ($singleCustomer['InvoiceDetails'] as $key => $invoiceDetails) {
                    if(empty($invoiceDetails['ProductCode'])){
                        file_put_contents(public_path('log/sap_invoice/invoice_details_missing-') . $dt .'-'.$singleCustomer['InvoiceNo']. '.txt', json_encode($invoiceDetails) . "\n", FILE_APPEND);
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
                        $details->TPRoundOff = $invoiceDetails['TPRoundOff'];
                        $details->VAT = $invoiceDetails['VAT'];
                        $details->DiscountPercentage = $invoiceDetails['DiscountPercentage'];
                        $details->Discount = $invoiceDetails['Discount'];
                        $details->save();
                    }
                }

                foreach ($singleCustomer['InvoiceDetailsBatch'] as $key => $invoiceDetailsBatch) {
                    if(empty($invoiceDetailsBatch['BatchNo'])  && empty($invoiceDetailsBatch['Quantity'])  && empty($invoiceDetailsBatch['SalesQTY'])      ){
                        file_put_contents(public_path('log/sap_invoice/invoice_details_batch_missing-') . $dt .'-'.$singleCustomer['InvoiceNo']. '.txt', json_encode($invoiceDetailsBatch) . "\n", FILE_APPEND);
                        return response()->json([
                            'status' => 'Error',
                            'message' => 'Missing Invoice Details Batch Parameter',
                            'InvoiceDetails' => $invoiceDetailsBatch
                        ], 422);
                    }
                    else{
                        $detailsBatch = new InvoiceDetailsBatch();
                        $detailsBatch->Invoiceno = $singleCustomer['InvoiceNo'];
                        $detailsBatch->ProductCode = !empty($invoiceDetailsBatch['ProductCode'])?$invoiceDetailsBatch['ProductCode']: $singleCustomer['InvoiceDetails'][$key]['ProductCode'];
                        $detailsBatch->BatchNo = $invoiceDetailsBatch['BatchNo'];
                        $detailsBatch->EngineNo = !empty($invoiceDetailsBatch['EngineNo']) ? $invoiceDetailsBatch['EngineNo'] : '';
                        $detailsBatch->Quantity = $invoiceDetailsBatch['Quantity'];
                        $detailsBatch->SalesQTY = $invoiceDetailsBatch['SalesQTY'];
                        $detailsBatch->Description = $invoiceDetailsBatch['Description'];
                        $detailsBatch->Colour = $invoiceDetailsBatch['Colour'];
                        $detailsBatch->Model = $invoiceDetailsBatch['Model'];
                        $detailsBatch->save();
                    }
                }

                DB::commit();

                file_put_contents(public_path('log/sap_invoice/sap_invoice_success-') . $dt . '.txt', json_encode($singleCustomer) . "\n", FILE_APPEND);
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Invoice Added Successfully',
                    'InvoiceNo' => $singleCustomer['InvoiceNo']
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
            file_put_contents(public_path('log/sap_invoice/sap_invoice_error-') . $dt . '.txt', $exception->getMessage() . '-' . $exception->getLine() . "\n", FILE_APPEND);
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    public function storeFlagShipInvoice (Request $request)
    {
        DB::beginTransaction();
        try{
            $singleCustomer = $request->all();
            if (!empty($singleCustomer['InvoiceNo'])
                && !empty($singleCustomer['ChassisNo'])
                && !empty($singleCustomer['CustomerName'])
                && !empty($singleCustomer['MobileNo'])
                && !empty($singleCustomer['FlagshipCode'])
            ){
                // Create a new record for each field
                $invoice = new FlagshipInvoiceBRTA();
                $invoice->InvoiceNo =    $singleCustomer['InvoiceNo'];
                $invoice->FlagshipCode = $singleCustomer['FlagshipCode'] ?? null;
                $invoice->MasterCode =   $singleCustomer['FlagshipCode'] ?? null;
                $invoice->CustomerCode = $singleCustomer['FlagshipCode'] ?? null;
                $invoice->CustomerName = $singleCustomer['CustomerName'];
                $invoice->FatherName = $singleCustomer['FatherName'] ?? null;
                $invoice->MotherName = $singleCustomer['MotherName'] ?? null;
                $invoice->PreAddress = $singleCustomer['PreAddress'] ?? null;
                $invoice->PerAddress = $singleCustomer['PerAddress'] ?? null;
                $invoice->MobileNo = $singleCustomer['MobileNo'];
                $invoice->Nationality = $singleCustomer['Nationality'] ?? null;
                $invoice->EmergencyMobile = $singleCustomer['EmergencyMobile'] ?? null;
                $invoice->BloodGroup = $singleCustomer['BloodGroup'] ?? null;
                $invoice->EMail = $singleCustomer['EMail'] ?? null;
                $invoice->NID = $singleCustomer['NID'] ?? null;
                $invoice->DateOfBirth = $singleCustomer['DateOfBirth'] ?? null;
                $invoice->Gender = $singleCustomer['Gender'] ?? null;
                $invoice->OwnerType = $singleCustomer['OwnerType'] ?? null;
                $invoice->ChassisNo = $singleCustomer['ChassisNo'];
                $invoice->EngineNo = $singleCustomer['EngineNo'] ?? null;
                $invoice->Color = $singleCustomer['Color'] ?? null;
                $invoice->FuelUsed = $singleCustomer['FuelUsed'] ?? null;
                $invoice->HorsePower = $singleCustomer['HorsePower'] ?? null;
                $invoice->RPM = $singleCustomer['RPM'] ?? null;
                $invoice->CubicCapacity = $singleCustomer['CubicCapacity'] ?? null;
                $invoice->WheelBase = $singleCustomer['WheelBase'] ?? null;
                $invoice->Weight = $singleCustomer['Weight'] ?? null;
                $invoice->WeightMax = $singleCustomer['WeightMax'] ?? null;
                $invoice->Standee = $singleCustomer['Standee'] ?? null;
                $invoice->TireSizeFront = $singleCustomer['TireSizeFront'] ?? null;
                $invoice->TireSizeRear = $singleCustomer['TireSizeRear'] ?? null;
                $invoice->Seats = $singleCustomer['Seats'] ?? null;
                $invoice->NoOfTyre = $singleCustomer['NoOfTyre'] ?? null;
                $invoice->NoOfAxel = $singleCustomer['NoOfAxel'] ?? null;
                $invoice->ClassOfVehicle = $singleCustomer['ClassOfVehicle'] ?? null;
                $invoice->MakerName = $singleCustomer['MakerName'] ?? null;
                $invoice->MakerCountry = $singleCustomer['MakerCountry'] ?? null;
                $invoice->EngineType = $singleCustomer['EngineType'] ?? null;
                $invoice->NumberOfCylinders = $singleCustomer['NumberOfCylinders'] ?? null;
                $invoice->ImportYear = $singleCustomer['ImportYear'] ?? null;
                $invoice->CreatedAt = $singleCustomer['CreatedAt'] ?? null;
                $invoice->ProductCode = $singleCustomer['ProductCode'] ?? null;
                $invoice->ProductName = $singleCustomer['ProductName'] ?? null;

                $invoice->save();
                DB::commit();

                return response()->json(['message' => 'Invoice created successfully!', 'InvoiceNo' => $singleCustomer['InvoiceNo']], 201);

            }
            else{
                // If required fields are missing
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Missing required fields: InvoiceNo, ChassisNo, CustomerName, or MobileNo.',
                    'Customer' => $singleCustomer
                ], 422);
            }

        }

        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }
}
