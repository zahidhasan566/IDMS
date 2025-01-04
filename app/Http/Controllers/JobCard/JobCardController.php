<?php

namespace App\Http\Controllers\JobCard;

use App\Http\Controllers\Controller;
use App\Models\DealerStock;
use App\Models\JobCard\DealarInvoiceDetails;
use App\Models\JobCard\DealarInvoiceMaster;
use App\Models\JobCard\FreeServiceSchedule;
use App\Models\JobCard\TblBaySetup;
use App\Models\JobCard\TblDealarMechanics;
use App\Models\JobCard\TblJobCard;
use App\Models\JobCard\TblJobCardDetailSparepartWork;
use App\Models\JobCard\TblJobCardProblemDetails;
use App\Models\JobCard\TblJobCardProblemStatement;
use App\Models\JobCard\TblJobStatus;
use App\Models\JobCard\TblJobType;
use App\Models\JobCard\TblTechnicianSetup;
use App\Models\JobCard\YtdFiNoStatus;
use App\Models\Product;
use App\Models\User;
use App\Services\FileBase64Service;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobCardController extends Controller
{
    use CommonTrait, CodeGeneration;

    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $tblJobCard = TblJobCard::select(
            'TblJobCard.Id',
            'TblJobCard.JobCardNo',
            'TblJobCard.SerialNo',
            DB::raw('convert(date,TblJobCard.JobDate) as JobDate'),
            'TblJobCard.CustomerName',
            'TblJobCard.MobileNo',
            'TblJobCard.Chassisno',
            'TblJobCard.EngineNo',
            'TblTechnicianSetup.TechnicianName',
            'tblBaySetup.BayName',
            'TblJobCard.JobStatus'
        )
            ->leftjoin('TblTechnicianSetup', function ($q) use ($userId) {
                $q->on('TblTechnicianSetup.TechnicianCode', 'TblJobCard.TechnicianCode');
                $q->where('TblTechnicianSetup.ServiceCenterCode', $userId);
            })
            ->leftjoin('TblBaySetup', function ($q) use ($userId) {
                $q->on('TblBaySetup.BayCode', 'TblJobCard.BayCode');
                $q->where('TblBaySetup.ServiceCenterCode', $userId);
            })
            ->where(function ($q) use ($search) {
                $q->where('TblJobCard.JobCardNo', 'like', '%' . $search . '%');
                $q->Orwhere('TblTechnicianSetup.TechnicianName', 'like', '%' . $search . '%');
                $q->Orwhere('tblBaySetup.BayName', 'like', '%' . $search . '%');
                $q->Orwhere('TblJobCard.ChassisNo', 'like', '%' . $search . '%');
            })
            ->where('TblJobCard.JobStatus', '!=', 'Close')
            ->orderBy('TblJobCard.Id', 'desc');

        if ($roleId !== 'admin') {
            $tblJobCard->where('TblJobCard.ServiceCenterCode', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $tblJobCard->get(),
            ]);
        } else {
            return $tblJobCard->paginate($take);
        }
    }

    public function getSupportingData()
    {
        $userId = Auth::user()->UserId;
        $jobCardNo = DB::select("exec usp_doLoadJobCardNoNew '$userId' ");
        $jobCardNo = $jobCardNo ? $jobCardNo[0]->JobCardNo : '';
        $allBay = TblBaySetup::select(
            'BayCode',
            'BayName',
            DB::raw("CONCAT(BayCode,'-',BayName) AS Details")
        )->where('ServiceCenterCode', $userId)->where('Active', 'Y')->get();
        $allTechnician = TblTechnicianSetup::select(
            'TblTechnicianSetup.DefaultBay',
            'TblBaySetup.BayName',
            'TblTechnicianSetup.TechnicianCode',
            'TblTechnicianSetup.TechnicianName',
            DB::raw("CONCAT(TblBaySetup.BayCode,'-',TblBaySetup.BayName) AS BayDetails"),
            DB::raw("CONCAT(TechnicianCode,'-',TechnicianName) AS Details")
        )
            ->leftjoin("TblBaySetup", function ($join) use ($userId) {
                $join->on("TblBaySetup.BayCode", "=", "TblTechnicianSetup.DefaultBay")
                    //->on("TblBaySetup.ServiceCenterCode", "=", "TblTechnicianSetup.ServiceCenterCode")
                    ->where('TblBaySetup.ServiceCenterCode', $userId)
                    ->where('TblTechnicianSetup.Active', 'Y');
            })
            ->where('TblTechnicianSetup.ServiceCenterCode', $userId)
            ->get();


        $parentJobType = TblJobType::select('Id', 'JobTypeName', 'ParentId', 'Active')->where('Active', 'Y')
            ->where('ParentId', 0)->orderBy('ReportOrder', 'asc')->get();

        $allJobType = TblJobType::select('Id', 'JobTypeName', 'ParentId', 'Active')->where('Active', 'Y')->orderBy('ReportOrder', 'asc')->get();
        $tblJobStatus = TblJobStatus::select('TblJobStatus.*')->where('Active', 'Y')->get();
        $allProblems = TblJobCardProblemStatement::select('tblJobCardProblemStatement.*')->where('Active', 'Y')->get();
        $ytdNoReason = YtdFiNoStatus::where('Category', 'YTD')->get();
        $fiNoReason = YtdFiNoStatus::where('Category', 'FI')->get();

//        $sql = "SELECT * FROM tblJobCardProblemStatement";
//        $allProblems =  DB::select($sql);

//        dd($test);

        $localMechanics = TblDealarMechanics::select(
            'TblDealarMechanics.MechanicsID',
            'TblDealarMechanics.MechanicsCode',
            'TblDealarMechanics.MechanicsName',
            DB::raw("CONCAT(TblDealarMechanics.MechanicsCode,'-',TblDealarMechanics.MechanicsName) AS MechanicsDetails")
        )
            ->where('TblDealarMechanics.ServiceCenterCode', $userId)
            ->get();

        return response()->json([
            'jobCardNo' => $jobCardNo,
            'localMechanics' => $localMechanics,
            'allBay' => $allBay,
            'tblJobStatus' => $tblJobStatus,
            'allTechnician' => $allTechnician,
            'allProblems' => $allProblems,
            'parentJobType' => $parentJobType,
            'allJobType' => $allJobType,
            'ytdNoReason' => $ytdNoReason,
            'fiNoReason' => $fiNoReason,
        ]);
    }

    public function checkChassisNo($chassisNo)
    {
        $userId = Auth::user()->UserId;
        $docheck = '%';
        if (!empty($chassisNo)) {
            try {
                $bikeList = DB::select("exec usp_doLoadCustomerDetails '$chassisNo','$docheck'");
                $onGoingStatusCheck = TblJobCard::select('TblJobCard.JobStatus','TblJobCard.ServiceCenterCode','Customer.CustomerName')
                                    ->join('Customer','Customer.CustomerCode','TblJobCard.ServiceCenterCode')
                                    ->where('ChassisNo', $chassisNo)
                                    ->where('TblJobCard.JobStatus','!=', 'Close')
                                    ->first();


            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage()
                ], 500);
            }
        } else {
            $bikeList = [];
        }

        return response()->json([
            'bikeList' => $bikeList,
            'onGoingStatusCheck' => $onGoingStatusCheck,
        ]);
    }

    public function checkOnlineBookingNo($bookingNo){
        $onlineBooking= DB::connection('dbYamahaServiceCenter')->table('tblOnlineBooking')
            ->select('tblOnlineBooking.ChassisNo')->where('tblOnlineBooking.ReservationNo', $bookingNo)->first();
        return response()->json([
            'onlineBooking' => $onlineBooking,
        ]);

    }

    public function checkLastServiceHistory($chassisNo)
    {
        $userId = Auth::user()->UserId;
        $docheck = '%';
        $data = [];
        if (!empty($chassisNo)) {
            try {
                $data['lastService'] = DB::select("exec usp_doloadLastServiceHistory '$chassisNo' ");
                $data['maxMileage'] = DB::select(DB::raw("select ISNULL(max(CONVERT(NUMERIC(18,0),Mileage)) + 1,0) AS max from tblJobCard where ChassisNo = '$chassisNo' and ISNUMERIC(Mileage)  = 1"));
                $data['freeServices'] = FreeServiceSchedule::select(
                    'FreeServiceSchedule.ChassisNo',
                    'FreeServiceSchedule.ScheduleTitle as JobTypeName',
                    'FreeServiceSchedule.FreeSScheduleID as Id',
                    'FreeServiceSchedule.Status',
                )
                    ->where('FreeServiceSchedule.ChassisNo', $chassisNo)->where('FreeServiceSchedule.Status', 0)
                    ->orderBy('FreeSScheduleID', 'asc')
                    ->get();
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'data' => $data,
        ]);

    }

    public function checkSpareParts($productCode)
    {
        $userId = Auth::user()->UserId;
        $business = 'P';
        $data = [];

        if (!empty($productCode)) {
            try {
                $loadSpareParts = $this->searchParts($productCode, $business);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'data' => $loadSpareParts,
        ]);
    }

    public function checkService($searchServiceName)
    {

        $userId = Auth::user()->UserId;
        $business = 'P';
        $data = [];

        if (!empty($searchServiceName)) {
            try {
                $loadServices = $this->searchService($searchServiceName, $business);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'data' => $loadServices,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jobCardNo' => 'required',
            'jobDate' => 'required',
            'chassisNo' => 'required',
            'mileage' => 'required',
            'technicianCode' => 'required',
            'bayCode' => 'required',
            'jobStatus' => 'required',
            'jobType' => 'required',
            'ServiceNo' => 'required',
            'ytdStatus' => 'required',
//            'fiStatus' => 'required',
            'timeReqMin' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }


        try {
            //Store TblJobCard
            DB::beginTransaction();
            $userId = Auth::user()->UserId;
            $jobCardNo = DB::select("exec usp_doLoadJobCardNoNew '$userId' ");
            $jobCardNo = $jobCardNo ? $jobCardNo[0]->JobCardNo : '';

            $tblJobCard = new TblJobCard();
            $tblJobCard->JobCardNo = $jobCardNo;
            $tblJobCard->JobDate = $request->jobDate;
            $tblJobCard->JobDateTime = Carbon::now();
            $tblJobCard->CustomerName = $request->customerName;
            $tblJobCard->PurchaseDate = $request->purchaseDate;
            $tblJobCard->MobileNo = $request->mobileNo;
            $tblJobCard->ChassisNo = $request->chassisNo ? $request->chassisNo['chassisno'] : '';
            $tblJobCard->RegistrationNo = $request->registrationNo;
            $tblJobCard->SerialNo = $request->serial;
            $tblJobCard->EngineNo = $request->engineNo;
            $tblJobCard->Brand = $request->brand;
            $tblJobCard->Model = $request->model;
            $tblJobCard->Mileage = $request->mileage;
            $tblJobCard->UnderWarrenty = $request->underWarrenty;
            $tblJobCard->Address = $request->address;
            $tblJobCard->ProblemDetails = $request->otherProblem;
            $tblJobCard->MotorcycleOuterCondition = $request->failureAnalysis;
            $tblJobCard->ReasonProlemRepairDetails = $request->reasonAndProblemDetails;
            $tblJobCard->TechnicianCode = $request->technicianCode ? $request->technicianCode['TechnicianCode'] : '';
            $tblJobCard->BayCode = $request->bayCode ? $request->bayCode['BayCode'] : '';
            $tblJobCard->TimeRequired = $request->timeReqMin;
            $tblJobCard->TimeTaken = $request->timeTaken;
            $tblJobCard->JobStatus = $request->jobStatus ? $request->jobStatus['StatusCode'] : '';
            $tblJobCard->JobtypeId = $request->jobType ? $request->jobType['Id'] : '';
            $tblJobCard->FreeSScheduleID = $request->jobType['Id'] == '2' ? $request->ServiceNo['Id'] : 0;
            $tblJobCard->ServiceCenterCode = $userId;
            $tblJobCard->CustomerFeedback = null;
            $tblJobCard->DiscountType = $request->discountType;
            $tblJobCard->ACIEmployeeId = $request->staffId;
            $tblJobCard->DiscountPercent = $request->discount;
            $tblJobCard->ReservationNo = null;
            if($request->jobStatus['StatusCode']==='Ongoing'){
                $tblJobCard->JobStartTime =  Carbon::now();
            }
            else{
                $tblJobCard->JobStartTime =  null;
            }


            $tblJobCard->JobEndTime = null;
            $tblJobCard->LocalMechanicsCode = !empty($request->reference) ? $request->reference['MechanicsCode'] : '';
            $tblJobCard->IUser = $userId;
            $tblJobCard->IDate = Carbon::now();
            $tblJobCard->YTD_status = $request->ytdStatus;

            $tblJobCard->YTD_status_no_reason = $request->ytdStatus;
            $tblJobCard->FI_Status = '';
            $tblJobCard->FI_status_no_reason = '';
            $tblJobCard->Signature = null;
            if ($request->ydTFile) {
                $tblJobCard->YTD_File = FileBase64Service::fileUpload($request->ydTFile, 'jobCardYdt', public_path('uploads/JobCardYdt/'));
            }
            $tblJobCard->SignatureBefore = null;
            $tblJobCard->SignatureAfter = null;
            $tblJobCard->SignatureSupervisor = null;

            $tblJobCard->save();
            //Job Type
            if ($request->jobType['Id'] === '2') {
                FreeServiceSchedule::where('FreeSScheduleID', $request->ServiceNo['Id'])->update([
                    'Status' => 1
                ]);
            }

            //Insert Problem Details
            if (!empty($request->problemId)) {
                foreach ($request->problemId as $singleProblem) {
                    $problemDetails = new TblJobCardProblemDetails();
                    $problemDetails->JobCardNo = $jobCardNo;
                    $problemDetails->ProblemDetailsName = $singleProblem['ProblemStatement'];
                    $problemDetails->save();
                }
            }
            Db::commit();
            //Insert tbl JobCard Detail Spare Part Work
            if (!empty($request->partsFields) || !empty($request->serviceFields)) {
                $sparePartsWorkStatus = $this->storeSparePartsWork($request, $jobCardNo);
                if ($sparePartsWorkStatus !== 'Success') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to Insert Spare Parts'
                    ], 500);
                }
            }
            //Send Sms To The User
//            file_put_contents('public/log/jobCard/jobCard_create-' .$jobCardNo. '.txt', json_encode($request->all()) . "\n", FILE_APPEND);

            return response()->json([
                'status' => 'Success',
                'message' => 'Job Card Added Successfully'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function storeSparePartsWork($request, $jobCardNo)
    {
        try {
            //Insert Parts table job card spare part Work
            if (!empty($request->partsFields)) {
                foreach ($request->partsFields as $singleParts) {
                    if ($singleParts['partsCode'] !== null) {
                        if (isset($singleParts['partsCode']['ProductCode'])) {
                            $itemCode = $singleParts['partsCode']['ProductCode'];
                        } else {
                            $itemCode = $singleParts['partsCode'][0]['ProductCode'];
                        }
                        if (!empty($itemCode)) {
                            $tblJobCardSpareParts = new TblJobCardDetailSparepartWork();
                            $tblJobCardSpareParts->JobCardNo = $jobCardNo;
                            $tblJobCardSpareParts->ItemType = 'Parts';
                            $tblJobCardSpareParts->ItemCode = $itemCode;
                            $tblJobCardSpareParts->Quantity = $singleParts['quantity'];
                            $tblJobCardSpareParts->UnitPrice = $singleParts['unitPrice'];
                            $tblJobCardSpareParts->TotalPrice = $singleParts['totalPrice'];
                            $tblJobCardSpareParts->ServiceCharge = $singleParts['serviceCharge'];
                            $tblJobCardSpareParts->Discount = $singleParts['discount'];
                            $tblJobCardSpareParts->save();
                        }
                    }

                }

            }
            if (!empty($request->serviceFields)) {
                foreach ($request->serviceFields as $singleService) {
                    if ($singleService['workCode'] !== null) {
                        if (isset($singleService['workCode']['WorkCode'])) {
                            $workCode = $singleService['workCode']['WorkCode'];
                        } else {
                            $workCode = $singleService['workCode'][0]['WorkCode'];
                        }
                        if (!empty($workCode)) {
                            $tblJobCardService = new TblJobCardDetailSparepartWork();
                            $tblJobCardService->JobCardNo = $jobCardNo;
                            $tblJobCardService->ItemType = 'Work';
                            $tblJobCardService->ItemCode = $workCode;
                            $tblJobCardService->Quantity = 1;
                            $tblJobCardService->UnitPrice = $singleService['unitPrice'];
                            $tblJobCardService->TotalPrice = $singleService['unitPrice'];
                            $tblJobCardService->ServiceCharge = 0;
                            $tblJobCardService->Discount = $singleService['serviceDiscount'];
                            $tblJobCardService->save();
                        }
                    }


                }
            }
            return 'Success';
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }

    }

    public function jobClose(Request $request)
    {
        try {
            $jobCardNo = $request->jobCardNo;
//            $invoiceNo = $this->generateJobCardInvoiceNo();
            $userId = Auth::user()->UserId;
            $existingJobCard = TblJobCard::where('JobCardNo', $jobCardNo)->first();



            $checkJobCard = DealarInvoiceMaster::where('FatherName',$jobCardNo)->first();
//            file_put_contents('public/log/jobCard/jobCard_close-' .$jobCardNo. '.txt', json_encode($request->all()) . "\n", FILE_APPEND);
            if(empty($checkJobCard)){
                //Dealer Invoice Master
                $dealerInvoiceMaster = new  DealarInvoiceMaster();
                $dealerInvoiceMaster->MasterCode = $userId;
                $dealerInvoiceMaster->InvoiceNo = $jobCardNo;
                $dealerInvoiceMaster->InvoiceDate = Carbon::now()->format('Y-m-d');
                $dealerInvoiceMaster->InvoiceTime = Carbon::now();
                $dealerInvoiceMaster->CustomerCode = '';
                $dealerInvoiceMaster->CustomerName = $existingJobCard->CustomerName;
                $dealerInvoiceMaster->FatherName = $request->jobCardNo;
                $dealerInvoiceMaster->MotherName = '';
                $dealerInvoiceMaster->PreAddress = '';
                $dealerInvoiceMaster->MobileNo = '';
                $dealerInvoiceMaster->EMail = '';
                $dealerInvoiceMaster->MobileNo = '';
                $dealerInvoiceMaster->NID = '';
                $dealerInvoiceMaster->IPAddress = '';
                $dealerInvoiceMaster->Picture = '';
                $dealerInvoiceMaster->DateOfBirth = null;
                $dealerInvoiceMaster->Picture = '';
                $dealerInvoiceMaster->MerriageDay = null;
                $dealerInvoiceMaster->VerifyCode = '';
                $dealerInvoiceMaster->InstallmentSize = '';
                $dealerInvoiceMaster->ExchangeBrandCode = '';
                $dealerInvoiceMaster->ExchangeEngineNo = '';
                $dealerInvoiceMaster->ExchangeChassisNo = '';
                $dealerInvoiceMaster->ExchangeMedium = '';
                $dealerInvoiceMaster->ResellerName = '';
                $dealerInvoiceMaster->ResellerContact = '';
                $dealerInvoiceMaster->MSRStaffId = '';
                $dealerInvoiceMaster->ReferanceNumber = '';
                $dealerInvoiceMaster->LocalMechanicsCode = '';
                $dealerInvoiceMaster->ProductIntroducingMedia = '';
                $dealerInvoiceMaster->InterestInProduct = '';
                $dealerInvoiceMaster->PreviouslyUsedBike = '';
                $dealerInvoiceMaster->PreviousBikeCC = '';
                $dealerInvoiceMaster->PreviousBikeUsage = '';
                $dealerInvoiceMaster->CauseForBuyingNewBike = '';
                $dealerInvoiceMaster->DistrictCode = '';
                $dealerInvoiceMaster->UpazillaCode = '';
                $dealerInvoiceMaster->SalesStaffName = '';
                $dealerInvoiceMaster->Gender = '';
                $dealerInvoiceMaster->OwnerType = '';
                $dealerInvoiceMaster->save();

                $existingParts = TblJobCardDetailSparepartWork::where('JobCardNo', $jobCardNo)->where('ItemType', 'Parts')->get();
                    foreach ($existingParts as $singleParts) {
                        $productName = Product::select('ProductName')->where('ProductCode', $singleParts['ItemCode'])->first();
                        $dealerInvoiceDetails = new DealarInvoiceDetails();
                        $dealerInvoiceDetails->InvoiceID = $dealerInvoiceMaster->InvoiceID;
                        $dealerInvoiceDetails->ProductCode = $singleParts['ItemCode'];
                        $dealerInvoiceDetails->ProductName = isset($productName['ProductName']) ? $productName['ProductName'] : '';
                        $dealerInvoiceDetails->Quantity = intval($singleParts['Quantity']);
                        $dealerInvoiceDetails->UnitPrice = intval($singleParts['UnitPrice']);
                        $dealerInvoiceDetails->VAT = 0;
                        $dealerInvoiceDetails->Discount =intval($singleParts['Discount']);
                        $dealerInvoiceDetails->save();

                        //Stock Remove
                        $dealerCurrentStock = DealerStock::select('CurrentStock')->where('MasterCode', $userId)->where('ProductCode', $singleParts['ItemCode'])->first();
                        if (!empty($dealerCurrentStock->CurrentStock)) {
                            DealerStock::where('MasterCode', $userId)->where('ProductCode', $singleParts['ItemCode'])->update([
                                'CurrentStock' => intval($dealerCurrentStock->CurrentStock) - intval($singleParts['Quantity'])
                            ]);
                        }
                    }
            }
            //Job Status Close
            $existingJobCard->JobStatus = 'Close';
            $existingJobCard->JobEndTime = Carbon::now();
            $existingJobCard->TimeTaken =  $existingJobCard->JobEndTime->diffInMinutes($existingJobCard->JobStartTime);
            $existingJobCard->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Job Card Closed Successfully'
            ], 200);



        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage() . 'Line No: ' . $exception->getLine()
            ], 500);
        }
    }

    public function existingJobCard($jobCardNo)
    {

        //$userId = Auth::user()->UserId;
        $user = TblJobCard::select('ServiceCenterCode')->where('JobCardNo', $jobCardNo)->first();
        $userId = $user->ServiceCenterCode;

        $sql = "exec usp_loadExistingJobCardInfo '$userId','$jobCardNo' ";
        $existingJobCard = $this->getPdoResult($sql);
        $existingJobCardInfo = $existingJobCard ? $existingJobCard[0] : [];
        $existingJobCardPartsInfo = $existingJobCard ? $existingJobCard[1] : [];
        $existingJobCardServiceInfo = $existingJobCard ? $existingJobCard[2] : [];

        //Img exist or not
        if($existingJobCard){

            $signatureBefore =  get_headers("https://dms.ifadmotors.com/apps/dms_signature/public/uploads/".$existingJobCardInfo[0]['SignatureBefore']);
            if ($signatureBefore[0] == 'HTTP/1.1 200 OK') {
                $signatureBefore = "";
            } else {
                $signatureBefore= "";
            }

            $signatureAfter =  get_headers("https://dms.ifadmotors.com/apps/dms_signature/public/uploads/".$existingJobCardInfo[0]['SignatureAfter']);
            if ($signatureAfter[0] == 'HTTP/1.1 200 OK') {
                $signatureAfter = "https://dms.ifadmotors.com/apps/dms_signature/public/uploads/".$existingJobCardInfo[0]['SignatureAfter'];
            } else {
                $signatureAfter= "";
            }

            $signatureSupervisor =  get_headers("https://dms.ifadmotors.com/dms_signature/public/uploads/".$existingJobCardInfo[0]['SignatureSupervisor']);
            if ($signatureSupervisor[0] == 'HTTP/1.1 200 OK') {
                $signatureSupervisor = "https://dms.ifadmotors.com/public/uploads/".$existingJobCardInfo[0]['SignatureSupervisor'];
            } else {
                $signatureSupervisor= "";
            }

        }

        return response()->json([
            'existingJobCard' => $existingJobCardInfo,
            'existingJobCardPartsInfo' => $existingJobCardPartsInfo,
            'existingJobCardServiceInfo' => $existingJobCardServiceInfo,
            'signatureBefore' => $signatureBefore,
            'signatureAfter' => $signatureAfter,
            'signatureSupervisor' => $signatureSupervisor,
        ]);
    }

    public function updateJobCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jobCardNo' => 'required',
            'jobDate' => 'required',
            'chassisNo' => 'required',
            'mileage' => 'required',
            'technicianCode' => 'required',
            'bayCode' => 'required',
            'jobStatus' => 'required',
            'jobType' => 'required',
            'ServiceNo' => 'required',
            'timeReqMin' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'error' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {

            //Update JobCard
            $userId = Auth::user()->UserId;

            if (isset($request->jobStatus['StatusCode'])) {
                $jobStatus = $request->jobStatus['StatusCode'];
            } else {
                $jobStatus = $request->jobStatus[0]['StatusCode'];
            }
            //For Ydt File
            $jobFile = TblJobCard::where('JobCardNo', $request->jobCardNo)->first();

            if($jobFile->JobStartTime===null && $jobStatus =='OnGoing'){
                $jobStartTime = Carbon::now();
            }else{
                $jobStartTime =  $jobFile->JobStartTime;
            }

            TblJobCard::where('JobCardNo', $request->jobCardNo)->update([
                'JobDate' => $request->jobDate,
                'JobDateTime' => Carbon::now(),
                'CustomerName' => $request->customerName,
                'PurchaseDate' => $request->purchaseDate,
                'MobileNo' => $request->mobileNo,
                'ChassisNo' => $request->chassisNo['chassisno'],
                'RegistrationNo' => $request->registrationNo,
                'SerialNo' => $request->serial,
                'EngineNo' => $request->engineNo,
                'Brand' => $request->brand,
                'Model' => $request->model,
                'Mileage' => $request->mileage,
                'UnderWarrenty' => $request->underWarrenty,
                'Address' => $request->address,
                'ProblemDetails' => $request->otherProblem,
                'MotorcycleOuterCondition' => $request->failureAnalysis,
                'ReasonProlemRepairDetails' => $request->reasonAndProblemDetails,
                'TechnicianCode' => $request->technicianCode['TechnicianCode'],
                'BayCode' => $request->bayCode['BayCode'],
                'TimeRequired' => $request->timeReqMin,
                'TimeTaken' => $request->timeTaken,
                'JobStatus' => $jobStatus,
                'JobtypeId' => $request->jobType['Id'],
                'FreeSScheduleID' => $request->jobType['Id'] === '2' ? $request->ServiceNo['Id'] : 0,
                'ServiceCenterCode' => $userId,
                'DiscountType' => $request->discountType,
                'ACIEmployeeId' => $request->staffId,
                'DiscountPercent' => $request->discount,
                'JobStartTime' => $jobStartTime,
                'JobEndTime' => null,
                'LocalMechanicsCode' => !empty($request->reference['MechanicsCode']) ? $request->reference['MechanicsCode'] : '',
                'IUser' => $userId,
                'IDate' => Carbon::now(),
//                'YTD_File' => $request->ydTFile ? FileBase64Service::fileUpload($request->ydTFile, 'jobCardYdt', public_path('uploads/JobCardYdt/')) : $jobFile->YTD_File,
//                'YTD_status' => $request->ytdStatus,
                'FI_Status' => '',
                'YTD_status_no_reason' => $request->ytdStatus === 'N' ? $request->reasonOfYDT['Id'] : null,
                'FI_status_no_reason' => $request->fiStatus === 'N' ? $request->reasonOfFI['Id'] : null,
            ]);


            //Job Type
            if ($request->jobType['Id'] === '2') {
                if (!empty($request->ServiceNo['Id'])) {
                    $serviceNo = $request->ServiceNo['Id'];
                } else {
                    $serviceNo = $request->ServiceNo[0]['Id'];
                }
                FreeServiceSchedule::where('FreeSScheduleID', $serviceNo)->update([
                    'Status' => 1
                ]);
            }

            //Delete Problem Details
            DB::table('TblJobCardProblemDetails')->where('JobCardNo', $request->jobCardNo)->delete();
            //Insert Problem Details
            if (!empty($request->problemId)) {
                foreach ($request->problemId as $singleProblem) {
                    $problemDetails = new TblJobCardProblemDetails();
                    $problemDetails->JobCardNo = $request->jobCardNo;
                    $problemDetails->ProblemDetailsName = $singleProblem['ProblemStatement'];
                    $problemDetails->save();
                }
            }

            if (!empty($request->partsFields) || !empty($request->serviceFields)) {
                //Delete Detail Spare Part Work
                DB::table('TblJobCardDetailSparepartWork')->where('JobCardNo', $request->jobCardNo)->delete();

                //Insert tbl JobCard Detail Spare Part Work
                $sparePartsWorkStatus = $this->storeSparePartsWork($request, $request->jobCardNo);
                if ($sparePartsWorkStatus !== 'Success') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unable to Insert Spare Parts'
                    ], 500);
                }
            }
            DB::commit();
            if ($jobStatus === 'Close') {
                $this->jobClose($request);
            }

            return response()->json([
                'status' => 'Success',
                'message' => 'Job Card Updated Successfully',
                'jobStatus' => $jobStatus,
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }


    public function jobCardDisplay()
    {
        $userId = Auth::user()->UserId;

        $tblJobCard = TblJobCard::select(
            'TblJobCard.SerialNo',
            DB::raw('convert(date,TblJobCard.JobDate) as JobDate'),
            'TblJobCard.CustomerName',
            'TblJobCard.JobStatus'
        )
//            ->join('TblTechnicianSetup',function ($q) use($userId) {
//                $q->on('TblTechnicianSetup.TechnicianCode','TblJobCard.TechnicianCode');
//                $q->where('TblTechnicianSetup.ServiceCenterCode',$userId);
//            })
//            ->join('TblBaySetup',function ($q) use($userId) {
//                $q->on('TblBaySetup.BayCode','TblJobCard.BayCode');
//                $q->where('TblBaySetup.ServiceCenterCode',$userId);
//            })
            ->where('TblJobCard.ServiceCenterCode', $userId)
            ->where('TblJobCard.JobStatus', '=', 'Ongoing')
            ->where('TblJobCard.SerialNo', '!=', '')
            ->orderBy('TblJobCard.SerialNo', 'asc');

        return response()->json([
            'data' => $tblJobCard->get(),
        ]);
    }

    public function getJobReportSupportingData()
    {
        $user = User::select('UserName as text', 'UserId as value')->where('UserId', Auth::user()->UserId)->get();
        $allJobType = TblJobType::select('JobTypeName as text', 'Id as value',)
            ->where('ParentId', '=', 0)
            ->where('Active', 'Y')->orderBy('ReportOrder', 'asc')->get();
        $tblJobStatus = TblJobStatus::select('TblJobStatus.StatusName as text', 'TblJobStatus.StatusCode as value')->where('Active', 'Y')->get();
        return response()->json([
            'user' => $user,
            'tblJobStatus' => $tblJobStatus,
            'allJobType' => $allJobType,
        ]);
    }

    public function getJobReportData(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $JobStatus = $request->JobStatus;
        $JobType = $request->JobType;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;

        $roleId = Auth::user()->RoleId;

        if (empty($request->CustomerCode) && $roleId !== 'admin') {
            $CustomerCode = Auth::user()->UserId;
        }

        if ($Export == 'Y') {
            $current_page = '%';
        }

        $sql = "exec usp_doLoadJobCardReport2 '$dateFrom','$dateTo','$CustomerCode','$JobStatus','$JobType',$PerPage,$CurrentPage";

        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());


        $NUmberOfRecord = $res[1][0]['NUmberOfRecord'];
        $pages_count = ceil($NUmberOfRecord / $PerPage);
        $last_page = $pages_count;
        $from = 1;
        $to = 20;
        if ($Export != 'Y') {
            $from = (($CurrentPage * $PerPage) + 1) - $PerPage;
            $to = ($CurrentPage * $PerPage);
        }
        $paginationData [] = [
            'current_page' => $CurrentPage,
            'last_page' => $last_page,
            'total' => (int)$NUmberOfRecord,
            'from' => $from,
            'to' => $to,
        ];

        return response()->json([
            'data' => $res[0],
            'paginationData' => $paginationData
        ]);

    }

    public function checkServiceHistory($chassisNo)
    {
        if (!empty($chassisNo)) {
            try {
                $serviceHistory = DB::select("exec usp_doLoadServiceHistoriesNew '$chassisNo'");
                $customerInfo = DB::select("exec usp_doLoadServiceCustomerInfo '$chassisNo'");

                if($serviceHistory ||$customerInfo ){
                    return response()->json([
                        'serviceHistory' => $serviceHistory,
                        'customerInfo' => $customerInfo[0],
                    ]);
                }
                else{
                    return response()->json([
                        'serviceHistory' => '',
                        'customerInfo' => '',
                    ]);
                }

            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage()
                ], 500);
            }
        }
    }

    public function checkServiceHistoryProblemDetails($jobCardNo, $dealerCode)
    {
        $userId = $dealerCode;
        $sql = "exec usp_loadExistingJobCardInfo '$userId','$jobCardNo' ";
        $existingJobCard = $this->getPdoResult($sql);
        $existingJobCardInfo = $existingJobCard ? $existingJobCard[0] : [];
        return response()->json([
            'existingJobCard' => $existingJobCardInfo,
        ]);
    }

    public function updateServiceHistory(Request $request)
    {
        try {
            TblJobCard::where('JobCardNo', $request->jobCardNo)->update([
                'Mileage' => $request->editMillage,
                'ProblemDetails' => $request->otherProblem,
                'ReasonProlemRepairDetails' => $request->reasonAndProblemDetails,
            ]);

            //Delete Problem Details
            DB::table('TblJobCardProblemDetails')->where('JobCardNo', $request->jobCardNo)->delete();
            //Insert Problem Details
            if (!empty($request->problemId)) {
                foreach ($request->problemId as $singleProblem) {
                    $problemDetails = new TblJobCardProblemDetails();
                    $problemDetails->JobCardNo = $request->jobCardNo;
                    $problemDetails->ProblemDetailsName = $singleProblem['ProblemStatement'];
                    $problemDetails->save();
                }
            }

            return response()->json([
                'status' => 'Success',
                'message' => 'Job Card Updated Successfully'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
}
