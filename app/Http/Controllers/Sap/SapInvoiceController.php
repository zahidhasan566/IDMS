<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\DealarInvoicePayment;
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

//    public function  storeFlagShipInvoice(Request $request)
//    {
//        DB::beginTransaction();
//        try {
//            $MasterCode = $request->CustomerCode;
//            $ChassisNo  = $request->ChassisNo['chassisno'];
//
//            //INVOICE EXIST CHECK
//            $exists = DB::table('DealarInvoiceMaster as DIM')->select('DID.ChassisNo','DIM.customername')
//                ->join('DealarInvoiceDetails as DID','DID.InvoiceID','=','DIM.InvoiceID')
//                ->where('DID.ChassisNo',$ChassisNo)
//                ->exists();
//
//            if ($exists){
//                return response()->json([
//                    'status' => 'success',
//                    'message' => 'Bike already sold.'
//                ]);
//            }
//
//            $invoiceDate  = date('Y-m-d', strtotime("now"));
//            $invoiceTime  = date('Y-m-d H:i:s', strtotime("now"));
//
////            $invoiceNo  = $this->generateInvoiceNo($MasterCode, $invoiceDate);
//            $invoiceNo = $this->generateJobCardInvoiceNo();
//            $VerifyCode = $this->generateVerifyCode();
//
//            //FOR IMAGE
//            if ($request->Photo) {
//                $image   = $request->Photo;
//                $name    = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
//                Image::make($image)->save(public_path('assets/images/person/').$name);
//            } else {
//                $name = '';
//            }
//
//            //NEWLY ADDED FIELDS
//            $productIntroducingMedia =  $request->ProductIntroducingMedia;
//            if (!empty($productIntroducingMedia)) {
//                $dataOne = [];
//                foreach ($productIntroducingMedia as $obj) {
//                    array_push($dataOne, $obj['Media']);
//                }
//                $productIntroducingMedia =  implode(",", $dataOne);
//            }
//
//            $interestInProduct =  $request->interestInProduct;
//            if (!empty($interestInProduct)) {
//                $dataTwo = [];
//                foreach ($interestInProduct as $obj) {
//                    array_push($dataTwo, $obj['InterestInProduct']);
//                }
//                $interestInProduct =  implode(",", $dataTwo);
//            }
//
//            $previouslyUsedBike =  $request->previouslyUsedBike;
//            if (!empty($previouslyUsedBike)) {
//                $dataThree = [];
//                foreach ($previouslyUsedBike as $obj) {
//                    array_push($dataThree, $obj['BikeName']);
//                }
//                $previouslyUsedBike =  implode(",", $dataThree);
//            }
//
//            $causeForBuyingNewBike =  $request->causeForBuyingNewBike;
//            if (!empty($causeForBuyingNewBike)) {
//                $dataFour = [];
//                foreach ($causeForBuyingNewBike as $obj) {
//                    array_push($dataFour, $obj['Cause']);
//                }
//                $causeForBuyingNewBike =  implode(",", $dataFour);
//            }
//
//            //DEALER INVOICE MASTER
//            $invoice                            = new DealarInvoiceMaster();
//            $invoice->MasterCode                = $MasterCode;
//            $invoice->InvoiceNo                 = $invoiceNo;
//            $invoice->InvoiceDate               = $invoiceDate;
//            $invoice->InvoiceTime               = $invoiceTime;
//            $invoice->CustomerCode              = $invoiceNo;
//            $invoice->CustomerName              = $request->CustomerName;
//            $invoice->FatherName                = $request->FatherName;
//            $invoice->MotherName                = $request->MotherName;
//            $invoice->MotherName                = $request->MotherName;
//            $invoice->PreAddress                = $request->Address;
//            $invoice->PerAddress                = !empty($request->PermanentAddress)?$request->PermanentAddress:$request->Address;
//            $invoice->MobileNo                  = $request->Mobile;
//            $invoice->EmergencyMobile            = $request->EmergencyMobile;
//            $invoice->BloodGroup                = $request->bloodGroup;
//            $invoice->EMail                     = isset($request->Email) ? $request->Email : '';
//            $invoice->NID                       = $request->NID;
//            $invoice->InquerySale               = isset($request->InquirySale) ? $request->InquirySale : 0;
//            $invoice->IPAddress                 = $_SERVER['SERVER_ADDR'];
//            $invoice->Picture                   = $name;
//            $invoice->DateOfBirth               = $request->DateOfBirth;
//            $invoice->MerriageDay               = isset($request->MarriageDay) ? $request->MarriageDay : null;
//            $invoice->VerifyCode                = $VerifyCode;
//            $invoice->IsEmi                     = $request->IsEmi;
//            $invoice->InstallmentSize           = $request->InstallmentSize;
//            $invoice->EMIBankID                 = $request->EmiBank;
//            $invoice->LocalMechanicsCode        = $request->MechanicsCode ? $request->MechanicsCode : '';
//            $invoice->EMIAmount                 = $request->EmiAmount;
//            $invoice->EMIInterestRate           = $request->InterestRate;
//            $invoice->EMIInterestPayable        = $request->InterestPayable;
//            $invoice->ExchangeBrandCode         = $request->ExchangeBrand ? $request->ExchangeBrand : '';
//            $invoice->ExchangeEngineNo          = $request->ExchangeEngineNo ? $request->ExchangeEngineNo : '';
//            $invoice->ExchangeChassisNo         = $request->ExchangeChassisNo ? $request->ExchangeChassisNo : '';
//            $invoice->OldBikeModel              = '';
//            $invoice->ModelName                 = $request->ModelName;
//            $invoice->BrandName                 = $request->BrandName;
//            $invoice->OldBikePrice              = $request->OldBikePrice;
//            $invoice->ExchangeMedium            = $request->ExchangeMedium;
//            $invoice->ExchangeCustomerDiscount  = $request->ExchangeCustomerDiscount;
//            $invoice->ResellerName              = $request->ResellerName;
//            $invoice->ResellerContact           = $request->ResellerContact;
//            $invoice->ResellerCommission        = $request->ResellerCommission;
//            $invoice->OccupationId              = $request->CustomerOccupation;
//            $invoice->MonthlyIncomeId           = $request->MonthlyIncome;
//            $invoice->ProductIntroducingMedia   = $productIntroducingMedia;
//            $invoice->InterestInProduct         = $interestInProduct;
//            $invoice->PreviouslyUsedBike        = $previouslyUsedBike;
//            $invoice->PreviousBikeCC            = $request->previousBikeCC;
//            $invoice->PreviousBikeUsage         = $request->previousBikeUsage;
//            $invoice->CauseForBuyingNewBike     = $causeForBuyingNewBike;
////            $invoice->REisKnown                 = '';
////            $invoice->REJoinYRC                 = '';
//            $invoice->DistrictCode              = $request->DistrictCode;
//            $invoice->UpazillaCode              = $request->ThanaCode;
//            $invoice->SalesStaffName            = $request->SalesStaffName ? $request->SalesStaffName : '';
//            $invoice->SalesStaffDesignation     = $request->SalesStaffDesignation ? $request->SalesStaffDesignation : '';
//            $invoice->Gender                    = $request->Gender;
//            $invoice->OwnerType                 = $request->OwnerTyp;
//            $invoice->AffiliatorCode            = '';
//            $invoice->AffiliatorDiscount        = 0;
//            $invoice->isSync                    = '';
//            $invoice->CSIResult                 = 0;
//
//
//            if ($invoice->save()){
//
//                //DEALER INVOICE DETAILS
//                $product = Product::query()->where('ProductCode',$request->ProductCode)->first();
//
//                $invoiceDetails                         = new DealarInvoiceDetails();
//                $invoiceDetails->InvoiceID              = $invoice->InvoiceID;
//                $invoiceDetails->InvoiceID              = $invoice->InvoiceID;
//                $invoiceDetails->ProductCode            = $request->ProductCode;
//                $invoiceDetails->ProductName            = $product->ProductName;
//                $invoiceDetails->Quantity               = 1;
//                $invoiceDetails->ProductName            = $product->ProductName;
//                $invoiceDetails->UnitPrice              = $product->MRP;
//                $invoiceDetails->VAT                    = 0;
//                $invoiceDetails->Discount               = $request->Discount;
//                $invoiceDetails->Discount               = $request->Discount;
//                $invoiceDetails->ChassisNo              = $ChassisNo;
//                $invoiceDetails->EngineNo               = $request->EngineNo;
//                $invoiceDetails->Color                  = $request->Color;
//                $invoiceDetails->FuelUsed               = $product->FuelUsed;
//                $invoiceDetails->HorsePower             = $product->HorsePower;
//                $invoiceDetails->RPM                    = $product->RPM;
//                $invoiceDetails->CubicCapacity          = $product->CubicCapacity;
//                $invoiceDetails->WheelBase              = $product->WheelBase;
//                $invoiceDetails->Weight                 = $product->Weight;
//                $invoiceDetails->TireSizeFront          = $product->TireSizeFront;
//                $invoiceDetails->Seats                  = 'Two';
//                $invoiceDetails->NoofTyre               = 'Two';
//                $invoiceDetails->NoofAxel               = 'Two';
//                $invoiceDetails->ClassOfVehicle         = 'Motorcycle';
//                $invoiceDetails->MakerName              = $product->Manufacturer;
//                $invoiceDetails->MakerCountry           = $product->Origin;
//                $invoiceDetails->EngineType             = '4 Stroke';
//                $invoiceDetails->NumberofCylinders      = 'One';
//                $invoiceDetails->ImportYear             = $product->ManufacturingYear;
//                $invoiceDetails->SalesType              = $request->SalesType;
//                $invoiceDetails->CreditAmount           = $request->CreditAmount;
//                $invoiceDetails->CreditTenureMonth      = $request->Tenure;
//                $invoiceDetails->save();
//
//                //UPDATE DEALER RECEIVE INVOICE DETAILS
//                DB::table('DealarReceiveInvoiceDetails')->where('ChassisNo',$ChassisNo)->update([
//                    'SoldQnty' => 1
//                ]);
//
//                //CREATE SCHEDULE
//                DB::statement("EXEC usp_FreeServiceScheduleInsert '$ChassisNo' ");
//
//                $cashAmount         = $request->CashAmount;
//                $cardData           = $request->tenderSelected;
//
//                //CASH PAYMENT
//                if ($request->cashSelected == true){
//                    $DealarInvoicePayment                   = new DealarInvoicePayment();
//                    $DealarInvoicePayment->InvoiceID        = $invoice->InvoiceID;
//                    $DealarInvoicePayment->PaymentType      = 'Cash';
//                    $DealarInvoicePayment->EMIBankID        = 0;
//                    $DealarInvoicePayment->Amount           = $cashAmount;
//                    $DealarInvoicePayment->SwipeRate        = 0;
//                    $DealarInvoicePayment->SwipeRateAmount  = 0;
//                    $DealarInvoicePayment->save();
//                }
//                //CARD PAYMENT
//                if ($request->cardSelected == true){
//                    foreach ($cardData as $item){
//                        $DealarInvoicePayment                   = new DealarInvoicePayment();
//                        $DealarInvoicePayment->InvoiceID        = $invoice->InvoiceID;
//                        $DealarInvoicePayment->PaymentType      = 'Card';
//                        $DealarInvoicePayment->EMIBankID        = $item['TenderId'];
//                        $DealarInvoicePayment->Amount           = $item['CardAmount'];
//                        $DealarInvoicePayment->SwipeRate        = $item['SwipeCharge'];
//                        $DealarInvoicePayment->SwipeRateAmount  = ($item['CardAmount'] / 100) * $item['SwipeCharge'];
//                        $DealarInvoicePayment->save();
//                    }
//                }
//
//                DB::commit();
//                return response()->json([
//                    'status'    => 'success',
//                    'message'   => 'Successfully Created'
//                ]);
//
//            }
//        }catch (\Exception $e){
//            file_put_contents('public/log/invoice_create.txt', $e->getMessage()."\n",FILE_APPEND);
//            DB::rollBack();
//            return response()->json([
//                'status' => 'error',
//                'message' => $e->getMessage() . '-' . $e->getLine()
//            ],500);
//        }
//
//    }
}
