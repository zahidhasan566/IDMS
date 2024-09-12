<?php

namespace App\Http\Controllers\Invoice;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Traits\CommonTrait;
use Jenssegers\Agent\Agent;
use App\Models\LostDocument;
use Illuminate\Http\Request;
use App\Traits\CodeGeneration;
use Illuminate\Support\Facades\DB;
use App\Models\LostDocumentDetails;
use App\Http\Controllers\Controller;
use App\Models\DealarInvoicePayment;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Models\DealarInvoiceDeleteLog;
use App\Http\Resources\InvoiceCollection;
use Illuminate\Support\Facades\Validator;
use App\Models\DealerReceiveInvoiceDetails;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Models\JobCard\FreeServiceSchedule;
use App\Models\JobCard\DealarInvoiceDetails;
use App\Http\Requests\Invoice\InvoiceRequest;

class InvoiceController extends Controller
{
    use CodeGeneration;
    use CommonTrait;

    public function getAllInvoice(Request $request){

        $ChassisNo  = $request->ChassisNo;
        $isAdmin    = Auth::user()->grpSup;
        $MasterCode = Auth::user()->UserId;

        $CurrentPage = $request->pagination['current_page'];
        $PerPage     = 20;
        $Export      = $request->Export;

        $DateFrom = $request->DateFrom;
        $DateTo   = $request->DateTo;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "EXEC SP_BikeInvoiceList '$MasterCode','$DateFrom','$DateTo','$ChassisNo','$PerPage','$CurrentPage'";
        //return $sql;
        $invoice = $this->getReportData($sql, $PerPage, $CurrentPage, $Export);

        return response()->json([
            'invoice' => $invoice,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function invoiceStore(InvoiceRequest $request){
        try {
            DB::beginTransaction();
            $MasterCode = Auth::user()->UserId;
            $ChassisNo  = $request->ChassisNo['chassisno'];

            //INVOICE EXIST CHECK
            $exists = DB::table('DealarInvoiceMaster as DIM')->select('DID.ChassisNo','DIM.customername')
                ->join('DealarInvoiceDetails as DID','DID.InvoiceID','=','DIM.InvoiceID')
                ->where('DID.ChassisNo',$ChassisNo)
                ->exists();
            if ($exists){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bike already sold.'
                ]);
            }

            $invoiceDate  = date('Y-m-d', strtotime("now"));
            $invoiceTime  = date('Y-m-d H:i:s', strtotime("now"));

//            $invoiceNo  = $this->generateInvoiceNo($MasterCode, $invoiceDate);
            $invoiceNo = $this->generateJobCardInvoiceNo();
            $VerifyCode = $this->generateVerifyCode();

            //FOR IMAGE
            if ($request->Photo) {
                $image   = $request->Photo;
                $name    = uniqid().time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->save(public_path('assets/images/person/').$name);
            } else {
                $name = '';
            }

            //NEWLY ADDED FIELDS
            $productIntroducingMedia =  $request->ProductIntroducingMedia;
            if (!empty($productIntroducingMedia)) {
                $dataOne = [];
                foreach ($productIntroducingMedia as $obj) {
                    array_push($dataOne, $obj['Media']);
                }
                $productIntroducingMedia =  implode(",", $dataOne);
            }

            $interestInProduct =  $request->interestInProduct;
            if (!empty($interestInProduct)) {
                $dataTwo = [];
                foreach ($interestInProduct as $obj) {
                    array_push($dataTwo, $obj['InterestInProduct']);
                }
                $interestInProduct =  implode(",", $dataTwo);
            }

            $previouslyUsedBike =  $request->previouslyUsedBike;
            if (!empty($previouslyUsedBike)) {
                $dataThree = [];
                foreach ($previouslyUsedBike as $obj) {
                    array_push($dataThree, $obj['BikeName']);
                }
                $previouslyUsedBike =  implode(",", $dataThree);
            }

            $causeForBuyingNewBike =  $request->causeForBuyingNewBike;
            if (!empty($causeForBuyingNewBike)) {
                $dataFour = [];
                foreach ($causeForBuyingNewBike as $obj) {
                    array_push($dataFour, $obj['Cause']);
                }
                $causeForBuyingNewBike =  implode(",", $dataFour);
            }

            //DEALER INVOICE MASTER
            $invoice                            = new DealarInvoiceMaster();
            $invoice->MasterCode                = $MasterCode;
            $invoice->InvoiceNo                 = $invoiceNo;
            $invoice->InvoiceDate               = $invoiceDate;
            $invoice->InvoiceTime               = $invoiceTime;
            $invoice->CustomerCode              = $invoiceNo;
            $invoice->CustomerName              = $request->CustomerName;
            $invoice->FatherName                = $request->FatherName;
            $invoice->MotherName                = $request->MotherName;
            $invoice->MotherName                = $request->MotherName;
            $invoice->PreAddress                = $request->Address;
            $invoice->PerAddress                = $request->Address;
            $invoice->MobileNo                  = $request->Mobile;
            $invoice->EMail                     = isset($request->Email) ? $request->Email : '';
            $invoice->NID                       = $request->NID;
            $invoice->InquerySale               = isset($request->InquirySale) ? $request->InquirySale : 0;
            $invoice->IPAddress                 = $_SERVER['SERVER_ADDR'];
            $invoice->Picture                   = $name;
            $invoice->DateOfBirth               = $request->DateOfBirth;
            $invoice->MerriageDay               = isset($request->MarriageDay) ? $request->MarriageDay : null;
            $invoice->VerifyCode                = $VerifyCode;
            $invoice->IsEmi                     = $request->IsEmi;
            $invoice->InstallmentSize           = $request->InstallmentSize;
            $invoice->EMIBankID                 = $request->EmiBank;
            $invoice->LocalMechanicsCode        = $request->MechanicsCode ? $request->MechanicsCode : '';
            $invoice->EMIAmount                 = $request->EmiAmount;
            $invoice->EMIInterestRate           = $request->InterestRate;
            $invoice->EMIInterestPayable        = $request->InterestPayable;
            $invoice->ExchangeBrandCode         = $request->ExchangeBrand ? $request->ExchangeBrand : '';
            $invoice->ExchangeEngineNo          = $request->ExchangeEngineNo ? $request->ExchangeEngineNo : '';
            $invoice->ExchangeChassisNo         = $request->ExchangeChassisNo ? $request->ExchangeChassisNo : '';
            $invoice->OldBikeModel              = '';
            $invoice->ModelName                 = $request->ModelName;
            $invoice->BrandName                 = $request->BrandName;
            $invoice->OldBikePrice              = $request->OldBikePrice;
            $invoice->ExchangeMedium            = $request->ExchangeMedium;
            $invoice->ExchangeCustomerDiscount  = $request->ExchangeCustomerDiscount;
            $invoice->ResellerName              = $request->ResellerName;
            $invoice->ResellerContact           = $request->ResellerContact;
            $invoice->ResellerCommission        = $request->ResellerCommission;
            $invoice->OccupationId              = $request->CustomerOccupation;
            $invoice->MonthlyIncomeId           = $request->MonthlyIncome;
            $invoice->ProductIntroducingMedia   = $productIntroducingMedia;
            $invoice->InterestInProduct         = $interestInProduct;
            $invoice->PreviouslyUsedBike        = $previouslyUsedBike;
            $invoice->PreviousBikeCC            = $request->previousBikeCC;
            $invoice->PreviousBikeUsage         = $request->previousBikeUsage;
            $invoice->CauseForBuyingNewBike     = $causeForBuyingNewBike;
            $invoice->YRCisKnown                = $request->YRCisKnown;
            $invoice->WantJoinYRC               = $request->wantJoinYRC;
            $invoice->DistrictCode              = $request->DistrictCode;
            $invoice->UpazillaCode              = $request->ThanaCode;
            $invoice->SalesStaffName            = $request->SalesStaffName ? $request->SalesStaffName : '';
            $invoice->SalesStaffDesignation     = $request->SalesStaffDesignation ? $request->SalesStaffDesignation : '';
            $invoice->Gender                    = $request->Gender;
            $invoice->OwnerType                 = $request->OwnerTyp;
            $invoice->AffiliatorCode            = '';
            $invoice->AffiliatorDiscount        = 0;
            $invoice->isSync                    = '';
            $invoice->CSIResult                 = 0;

            if ($invoice->save()){

                //DEALER INVOICE DETAILS
                $product = Product::query()->where('ProductCode',$request->ProductCode)->first();

                $invoiceDetails                         = new DealarInvoiceDetails();
                $invoiceDetails->InvoiceID              = $invoice->InvoiceID;
                $invoiceDetails->InvoiceID              = $invoice->InvoiceID;
                $invoiceDetails->ProductCode            = $request->ProductCode;
                $invoiceDetails->ProductName            = $product->ProductName;
                $invoiceDetails->Quantity               = 1;
                $invoiceDetails->ProductName            = $product->ProductName;
                $invoiceDetails->UnitPrice              = $product->MRP;
                $invoiceDetails->VAT                    = 0;
                $invoiceDetails->Discount               = $request->Discount;
                $invoiceDetails->Discount               = $request->Discount;
                $invoiceDetails->ChassisNo              = $ChassisNo;
                $invoiceDetails->EngineNo               = $request->EngineNo;
                $invoiceDetails->Color                  = $request->Color;
                $invoiceDetails->FuelUsed               = $product->FuelUsed;
                $invoiceDetails->HorsePower             = $product->HorsePower;
                $invoiceDetails->RPM                    = $product->RPM;
                $invoiceDetails->CubicCapacity          = $product->CubicCapacity;
                $invoiceDetails->WheelBase              = $product->WheelBase;
                $invoiceDetails->Weight                 = $product->Weight;
                $invoiceDetails->TireSizeFront          = $product->TireSizeFront;
                $invoiceDetails->Seats                  = 'Two';
                $invoiceDetails->NoofTyre               = 'Two';
                $invoiceDetails->NoofAxel               = 'Two';
                $invoiceDetails->ClassOfVehicle         = 'Motorcycle';
                $invoiceDetails->MakerName              = $product->Manufacturer;
                $invoiceDetails->MakerCountry           = $product->Origin;
                $invoiceDetails->EngineType             = '4 Stroke';
                $invoiceDetails->NumberofCylinders      = 'One';
                $invoiceDetails->ImportYear             = $product->ManufacturingYear;
                $invoiceDetails->SalesType              = $request->SalesType;
                $invoiceDetails->CreditAmount           = $request->CreditAmount;
                $invoiceDetails->CreditTenureMonth      = $request->Tenure;
                $invoiceDetails->save();

                //UPDATE DEALER RECEIVE INVOICE DETAILS
                DB::table('DealarReceiveInvoiceDetails')->where('ChassisNo',$ChassisNo)->update([
                    'SoldQnty' => 1
                ]);

                //CREATE SCHEDULE
                DB::statement("EXEC usp_FreeServiceScheduleInsert '$ChassisNo' ");

                $cashAmount         = $request->CashAmount;
                $cardData           = $request->tenderSelected;

                //CASH PAYMENT
                if ($request->cashSelected == true){
                    $DealarInvoicePayment                   = new DealarInvoicePayment();
                    $DealarInvoicePayment->InvoiceID        = $invoice->InvoiceID;
                    $DealarInvoicePayment->PaymentType      = 'Cash';
                    $DealarInvoicePayment->EMIBankID        = 0;
                    $DealarInvoicePayment->Amount           = $cashAmount;
                    $DealarInvoicePayment->SwipeRate        = 0;
                    $DealarInvoicePayment->SwipeRateAmount  = 0;
                    $DealarInvoicePayment->save();
                }
                //CARD PAYMENT
                if ($request->cardSelected == true){
                    foreach ($cardData as $item){
                        $DealarInvoicePayment                   = new DealarInvoicePayment();
                        $DealarInvoicePayment->InvoiceID        = $invoice->InvoiceID;
                        $DealarInvoicePayment->PaymentType      = 'Card';
                        $DealarInvoicePayment->EMIBankID        = $item['TenderId'];
                        $DealarInvoicePayment->Amount           = $item['CardAmount'];
                        $DealarInvoicePayment->SwipeRate        = $item['SwipeCharge'];
                        $DealarInvoicePayment->SwipeRateAmount  = ($item['CardAmount'] / 100) * $item['SwipeCharge'];
                        $DealarInvoicePayment->save();
                    }
                }

                //SEND MESSAGE
                $encodedInfo        = base64_encode($invoice->InvoiceID . '.' . $request->Mobile . '.' . $VerifyCode . '.' . $invoice->InvoiceID);
                $feedbackBaseUrl    = 'http://feedback.yamahabd.com/';
                $feedbackLink       = $feedbackBaseUrl . '?i=' . $encodedInfo;
                $smsText            = "Dear Customer: Thanks for buying from IFAD. You can give your valuable feedback through the link bellow." . "\n" . $feedbackLink;
                $this->sendSms($request->Mobile, $smsText);

                DB::commit();
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Successfully Created'
                ]);

            }
        }catch (\Exception $e){
            file_put_contents('public/log/invoice_create.txt', $e->getMessage()."\n",FILE_APPEND);
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function invoiceDelete($invoiceID){
        try {
            DB::beginTransaction();
            $MasterCode = Auth::user()->UserId;
            $agent = new Agent();
            $browser = $agent->browser();
            $version = $agent->version($browser);
            $browser_version = $browser.' '.$version;

            $platform = $agent->platform();
            $version = $agent->version($platform);
            $platform_version = $platform.' '.$version;

            $device_and_browser_info = $browser_version.' '.$platform_version;

            $DealerInvoiceMaster = DealarInvoiceMaster::query()->where('InvoiceID', $invoiceID)->first();
            $DealerInvoiceDetails = DealarInvoiceDetails::query()->where('InvoiceID', $invoiceID)->first();

            $DealerInvoiceDeleteLog = new DealarInvoiceDeleteLog();
            $DealerInvoiceDeleteLog->InvoiceID = $DealerInvoiceMaster->InvoiceID;
            $DealerInvoiceDeleteLog->InvDetailsID = $DealerInvoiceDetails->InvDetailsID;
            $DealerInvoiceDeleteLog->MasterCode = $MasterCode;
            $DealerInvoiceDeleteLog->InvoiceNo = $DealerInvoiceMaster->InvoiceNo;
            $DealerInvoiceDeleteLog->InvoiceDate = $DealerInvoiceMaster->InvoiceDate;
            $DealerInvoiceDeleteLog->InvoiceTime = $DealerInvoiceMaster->InvoiceTime;
            $DealerInvoiceDeleteLog->CustomerCode = $MasterCode;
            $DealerInvoiceDeleteLog->CustomerName = $DealerInvoiceMaster->CustomerName;
            $DealerInvoiceDeleteLog->FatherName = $DealerInvoiceMaster->FatherName;
            $DealerInvoiceDeleteLog->MotherName = $DealerInvoiceMaster->MotherName;
            $DealerInvoiceDeleteLog->PreAddress = $DealerInvoiceMaster->PreAddress;
            $DealerInvoiceDeleteLog->PerAddress = $DealerInvoiceMaster->PerAddress;
            $DealerInvoiceDeleteLog->MobileNo = $DealerInvoiceMaster->MobileNo;
            $DealerInvoiceDeleteLog->EMail = $DealerInvoiceMaster->EMail;
            $DealerInvoiceDeleteLog->NID = $DealerInvoiceMaster->NID;
            $DealerInvoiceDeleteLog->InquerySale = $DealerInvoiceMaster->InquerySale;
            $DealerInvoiceDeleteLog->IPAddress = $DealerInvoiceMaster->IPAddress;
            $DealerInvoiceDeleteLog->Picture = $DealerInvoiceMaster->Picture;
            $DealerInvoiceDeleteLog->DateOfBirth = $DealerInvoiceMaster->DateOfBirth;
            $DealerInvoiceDeleteLog->MerriageDay = $DealerInvoiceMaster->MerriageDay;
            $DealerInvoiceDeleteLog->VerifyCode = $DealerInvoiceMaster->VerifyCode;
            $DealerInvoiceDeleteLog->Verified = $DealerInvoiceMaster->Verified;
            $DealerInvoiceDeleteLog->IsEMI = $DealerInvoiceMaster->IsEMI;
            $DealerInvoiceDeleteLog->InstallmentSize = $DealerInvoiceMaster->InstallmentSize;
            $DealerInvoiceDeleteLog->EMIBankID = $DealerInvoiceMaster->EMIBankID;
            $DealerInvoiceDeleteLog->ProductCode = $DealerInvoiceDetails->ProductCode;
            $DealerInvoiceDeleteLog->ProductName = $DealerInvoiceDetails->ProductName;
            $DealerInvoiceDeleteLog->Quantity = $DealerInvoiceDetails->Quantity;
            $DealerInvoiceDeleteLog->UnitPrice = $DealerInvoiceDetails->UnitPrice;
            $DealerInvoiceDeleteLog->VAT = $DealerInvoiceDetails->VAT;
            $DealerInvoiceDeleteLog->Discount = $DealerInvoiceDetails->Discount;
            $DealerInvoiceDeleteLog->ChassisNo = $DealerInvoiceDetails->ChassisNo;
            $DealerInvoiceDeleteLog->EngineNo = $DealerInvoiceDetails->EngineNo;
            $DealerInvoiceDeleteLog->Color = $DealerInvoiceDetails->Color;
            $DealerInvoiceDeleteLog->FuelUsed = $DealerInvoiceDetails->FuelUsed;
            $DealerInvoiceDeleteLog->HorsePower = $DealerInvoiceDetails->HorsePower;
            $DealerInvoiceDeleteLog->RPM = $DealerInvoiceDetails->RPM;
            $DealerInvoiceDeleteLog->CubicCapacity = $DealerInvoiceDetails->CubicCapacity;
            $DealerInvoiceDeleteLog->WheelBase = $DealerInvoiceDetails->WheelBase;
            $DealerInvoiceDeleteLog->Weight = $DealerInvoiceDetails->Weight;
            $DealerInvoiceDeleteLog->TireSizeFront = $DealerInvoiceDetails->TireSizeFront;
            $DealerInvoiceDeleteLog->TireSizeRear = $DealerInvoiceDetails->TireSizeRear;
            $DealerInvoiceDeleteLog->Seats = $DealerInvoiceDetails->Seats;
            $DealerInvoiceDeleteLog->NoofTyre = $DealerInvoiceDetails->NoofTyre;
            $DealerInvoiceDeleteLog->NoofAxel = $DealerInvoiceDetails->NoofAxel;
            $DealerInvoiceDeleteLog->ClassOfVehicle = $DealerInvoiceDetails->ClassOfVehicle;
            $DealerInvoiceDeleteLog->MakerName = $DealerInvoiceDetails->MakerName;
            $DealerInvoiceDeleteLog->MakerCountry = $DealerInvoiceDetails->MakerCountry;
            $DealerInvoiceDeleteLog->EngineType = $DealerInvoiceDetails->EngineType;
            $DealerInvoiceDeleteLog->NumberofCylinders = $DealerInvoiceDetails->NumberofCylinders;
            $DealerInvoiceDeleteLog->ImportYear = $DealerInvoiceDetails->ImportYear;
            $DealerInvoiceDeleteLog->DeletedBy = $MasterCode;
            $DealerInvoiceDeleteLog->DeletedTime = Carbon::now();
            $DealerInvoiceDeleteLog->DeletedIpAddress = $_SERVER['SERVER_ADDR'];
            $DealerInvoiceDeleteLog->DeletedDiviceState = $device_and_browser_info;
            $DealerInvoiceDeleteLog->save();

            //UPDATE DEALER RECEIVE INVOICE DETAILS
            DB::table('DealarReceiveInvoiceDetails')->where('ChassisNo',$DealerInvoiceDetails->ChassisNo)->update([
                'SoldQnty' => 0
            ]);

            FreeServiceSchedule::query()->where('ChassisNo',$DealerInvoiceDetails->ChassisNo)->delete();
            DealarInvoicePayment::query()->where('InvoiceID',$invoiceID)->delete();
            $DealerInvoiceDetails->delete();
            $DealerInvoiceMaster->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Deleted'
            ]);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
            ],500);
        }

    }

    public function getSingleInvoice($invoiceId){
        $invoice = DB::table('DealarInvoiceMaster as DIM')
            ->select("DIM.InvoiceId","DIM.invoiceno", DB::raw("FORMAT(DIM.invoicedate,'yyyy-MM-dd') as InvoiceDate"), "DIM.invoicetime", "DIM.customercode", "DIM.customername",
                "DIM.fathername", "DIM.mothername", "DIM.preaddress", "DIM.peraddress", "DIM.mobileno", "DIM.EMail",
                "DIM.nid", "DID.productcode", "p.productname", "DID.quantity", "DID.unitprice",
                "DID.chassisno", "DID.engineno", "DID.color", "DID.fuelused", "DID.horsepower", "DID.rpm",
                "DID.cubiccapacity", "DID.discount", "DID.wheelbase", "DID.weight", "DID.tiresizefront",
                "DID.tiresizerear", "DID.seats", "DID.nooftyre", "DID.noofaxel", "DID.classofvehicle",
                "DID.makername", "DID.makercountry", "DID.enginetype", "DID.numberofcylinders",
                "C.CustomerCode", "C.CustomerName", "C.Add1", "P.Standee", "DIM.DateOfBirth", "P.BodyType", "P.WeightMax", "P.ManufacturingYear","P.ManufacturingCountry",
                "P.TireSizeFront", "P.TireSizeRear","P.ProductCode",DB::raw('isnull(YEAR(R.Inv_PODate),DID.importyear) AS importyear'),"DIM.gender","DIM.ownertype")
            ->join('DealarInvoiceDetails as DID','DID.InvoiceID','=','DIM.InvoiceID')
            ->leftJoin('ReceiveDetails as RD',function ($join){
                $join->on('RD.Batchno','=','DID.ChassisNo');
                $join->on('RD.ProductCode','=','DID.ProductCode');
            })
            ->leftJoin('Receive as R','RD.ReceiveNo','=','R.ReceiveNo')
            ->join('Product as P','DID.Productcode','=','P.Productcode')
            ->join('Customer as C','C.CustomerCode','=','DIM.MasterCode')
            ->where('DIM.InvoiceID',$invoiceId)
            ->first();
        return response([
           'invoice' => $invoice
        ]);
    }

    public function calculatePayableInterest(Request $request){
        $emibank         = $request->EmiBank;
        $emiamount       = $request->EmiAmount;
        $installmentsize = $request->InstallmentSize;
        $sql = "SELECT
                        CONVERT(NUMERIC(18,0), ((($emiamount/100) * InterestRate) / $installmentsize) * ($installmentsize - 6)) AS Interest, InterestRate
                FROM EMIBankInterestRate 
                WHERE EMIBankID = $emibank
                        AND InstallmentSize = $installmentsize";
        $result = DB::select($sql);
        if ($result){
            return response()->json([
                'data' => $result[0]
            ]);
        }else{
            return response()->json([
                'data' => [
                    'InterestRate' =>0,
                    'Interest' =>0
                ]
            ]);
        }
    }

    function sendSms($receipient, $smstext='Sample Text') {
        $ip = '192.168.100.213';
        $userId = 'motors';
        $password = 'Asdf1234';
        $smstext = urlencode($smstext);
        $smsUrl = "http://{$ip}/httpapi/sendsms?userId={$userId}&password={$password}&smsText=" . $smstext . "&commaSeperatedReceiverNumbers=" . $receipient;
        $smsUrl = preg_replace("/ /", "%20", $smsUrl);
        $response = file_get_contents($smsUrl);
        return json_decode($response);
    }

    public function DBConnectionQuery($sql){
        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());
        return $res;
    }

}
