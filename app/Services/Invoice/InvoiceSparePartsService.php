<?php

namespace App\Services\Invoice;

use App\Models\JobCard\DealarInvoiceDetails;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Models\JobCard\ReturnDealarInvoiceLog;
use App\Models\Product;
use App\Services\StockService;
use App\Traits\CodeGeneration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceSparePartsService
{
    public static function createInvoice($userId, $invoiceDate, $invoiceTime, $customerName, $customerAddress, $customerMobile, $ipAddress, $fields, $mechanics, $affiliatorCode, $affiliatorDiscount, $invoiceNo)
    {
        DB::beginTransaction();
        try {
            $invoice = new DealarInvoiceMaster();
            $invoice->MasterCode = $userId;
            $invoice->InvoiceNo = $invoiceNo;
            $invoice->InvoiceDate = $invoiceDate;
            $invoice->InvoiceTime = $invoiceTime;
            $invoice->CustomerCode = $invoiceNo;
            $invoice->CustomerName = $customerName;
            $invoice->FatherName = '';
            $invoice->MotherName = '';
            $invoice->PreAddress = empty($customerAddress) ? '' : $customerAddress;
            $invoice->PerAddress = empty($customerAddress) ? '' : $customerAddress;
            $invoice->MobileNo = empty($customerMobile) ? '' : $customerMobile;
            $invoice->EMail = '';
            $invoice->NID = '';
            $invoice->InquerySale = '';
            $invoice->IPAddress = $ipAddress;
            $invoice->Picture = '';
            $invoice->DateOfBirth = '';
            $invoice->MerriageDay = '';
            $invoice->LocalMechanicsCode = empty($mechanics) ? '' : $mechanics;
            $invoice->VerifyCode = '';
            $invoice->IsEmi = 0;
            $invoice->InstallmentSize = '';
            $invoice->EMIBankID = 0;
            $invoice->EMIAmount = 0;
            $invoice->EMIInterestRate = 0;
            $invoice->EMIInterestPayable = 0;
            $invoice->ExchangeBrandCode = '';
            $invoice->ExchangeEngineNo = '';
            $invoice->ExchangeChassisNo = '';
            $invoice->OccupationId = '';
            $invoice->DistrictCode = '';
            $invoice->UpazillaCode = '';
            $invoice->SalesStaffName = '';
            $invoice->SalesStaffDesignation = '';
            $invoice->Gender = '';
            $invoice->OwnerType = '';
            $invoice->AffiliatorCode = $affiliatorCode;
            $invoice->AffiliatorDiscount = $affiliatorDiscount;
            if ($invoice->save()) {
                foreach ($fields as $row) {
                    $product = Product::where('ProductCode',$row['model']['id'])->first();
                    if ($product) {
                        $invoiceDetails = new DealarInvoiceDetails();
                        $invoiceDetails->InvoiceID = $invoice->InvoiceID;
                        $invoiceDetails->ProductCode = $product->ProductCode;
                        $invoiceDetails->ProductName = $product->ProductName;
                        $invoiceDetails->Quantity = $row['quantity'];
                        $invoiceDetails->UnitPrice = $product->MRP;
                        $invoiceDetails->VAT = 0;
                        $invoiceDetails->Discount = $row['discount'];
                        $invoiceDetails->ChassisNo = '';
                        $invoiceDetails->EngineNo = '';
                        $invoiceDetails->Color = '';
                        $invoiceDetails->FuelUsed = '';
                        $invoiceDetails->HorsePower = '';
                        $invoiceDetails->RPM = '';
                        $invoiceDetails->CubicCapacity = '';
                        $invoiceDetails->WheelBase = '';
                        $invoiceDetails->Weight = '';
                        $invoiceDetails->TireSizeFront = '';
                        $invoiceDetails->Seats = '';
                        $invoiceDetails->NoofTyre = '';
                        $invoiceDetails->NoofAxel = '';
                        $invoiceDetails->ClassOfVehicle = '';
                        $invoiceDetails->MakerName = '';
                        $invoiceDetails->MakerCountry = '';
                        $invoiceDetails->EngineType = '';
                        $invoiceDetails->NumberofCylinders = '';
                        $invoiceDetails->ImportYear = '';
                        $invoiceDetails->save();
                        //STOCK UPDATE
                        $stockService = new StockService($userId,$product->ProductCode,$row['quantity']);
                        if (!$stockService->dealerProductStockOut()) {
                            DB::rollBack();
                            Log::error('Stock update failed!');
                            return false;
                        }
                    } else {
                        DB::rollBack();
                        Log::error('Product not found from the field list');
                        return false;
                    }
                }
                DB::commit();
                return $invoice->InvoiceID;
            }
            return false;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return false;
        }
    }

    public static function getInvoiceDetails($invoiceNo,$userId)
    {
        $invoiceMaster = DB::table('DealarInvoiceMaster','dim')->join('Customer as c','c.CustomerCode','dim.MasterCode')
            ->where('dim.InvoiceNo',$invoiceNo)->where('MasterCode',$userId)
            ->select('dim.InvoiceID','dim.invoiceno as InvoiceNo', 'dim.invoicedate as InvoiceDate', 'dim.invoicetime as InvoiceTime', 'dim.customername as CustomerName',
                'dim.preaddress as PreAddress', 'dim.mobileno as MobileNo', 'C.CustomerCode as CustomerCode', 'C.CustomerName as CustomerName', 'C.Add1', 'C.Add2', 'C.Mobile','AffiliatorCode','AffiliatorDiscount')
            ->first();
        if ($invoiceMaster) {
            $invoiceDetails = DB::table('DealarInvoiceDetails','did')->join('Product as p','p.ProductCode','did.ProductCode')
                ->where('did.InvoiceID',$invoiceMaster->InvoiceID)
                ->select('did.InvDetailsID', 'did.InvoiceID', 'did.ProductCode', 'p.ProductName',
                    'did.Quantity',
                    'did.Quantity as PastQuantity',
                    'did.UnitPrice',
                    DB::raw("CONVERT(int,did.Discount) as Discount"),
                    DB::raw("CONVERT(int,did.Discount) as PastDiscount"),
                    DB::raw("convert(int,(did.UnitPrice * did.Quantity) * (did.Discount/100)) as DiscountAmount"),
                    'did.VAT',
                    DB::raw("(did.UnitPrice * did.Quantity)-((did.UnitPrice * did.Quantity) * (did.Discount/100)) as TotalAmount"))
                ->get();
            return [
                'invoiceMaster' => $invoiceMaster,
                'invoiceDetails' => $invoiceDetails
            ];
        }
        return [];
    }

    public static function return($detail,$invoiceNo)
    {
        DB::beginTransaction();
        try {
            $invoiceDetails = DealarInvoiceDetails::where('InvDetailsID',$detail['InvDetailsID'])->first();
            $userId = Auth::user()->UserId;
            //InvoiceDetails UPDATE
            DealarInvoiceDetails::where('InvDetailsID',$detail['InvDetailsID'])->update([
                'Quantity' => !empty($detail['rQuantity']) ? $invoiceDetails->Quantity - intval($detail['rQuantity']) : 0,
                'Discount' => !empty($detail['Discount']) ? $invoiceDetails->Discount - intval($detail['Discount']) : 0
            ]);
            //RETURN LOG UPDATE
            $data['InvoiceID'] = $detail['InvoiceID'];
            $data['InvoiceDetailsID'] = $detail['InvDetailsID'];
            $data['InvoiceNo'] = $invoiceNo;
            $data['PrevQty'] = $invoiceDetails->Quantity;
            $data['CurrentQty'] = !empty($detail['rQuantity']) ? $detail['rQuantity'] : 0;
            $data['PrevDiscount'] = $invoiceDetails->Discount;
            $data['CurrentDiscount'] = !empty($detail['Discount']) ? $detail['Discount'] : 0;
            $data['ReturnBy'] = $userId;
            $data['ReturnDate'] = date('Y-m-d H:i:s');
            $data['ProductCode'] = $detail['ProductCode'];
            ReturnDealarInvoiceLog::create($data);
            //STOCK INCREASE
            $stockService = new StockService($userId,$detail['ProductCode'],$detail['rQuantity']);
            if ($stockService->dealerProductStockIn()) {
                DB::commit();
            } else {
                DB::rollBack();
                Log::error("Failed to commit.");
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return false;
        }
    }
}