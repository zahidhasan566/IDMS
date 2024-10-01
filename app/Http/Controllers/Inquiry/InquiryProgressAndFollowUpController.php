<?php

namespace App\Http\Controllers\Inquiry;

use App\Http\Controllers\Controller;
use App\Models\Inquiry\CompetitorCompany;
use App\Models\Inquiry\InquiryCustomerCategory;
use App\Models\Inquiry\InquiryDocument;
use App\Models\Inquiry\InquiryDocumentCategory;
use App\Models\Inquiry\InquiryLevel;
use App\Models\Inquiry\InquiryMainUser;
use App\Models\Inquiry\InquiryMaster;
use App\Models\Inquiry\InquiryMedia;
use App\Models\Inquiry\InquiryMediaCategory;
use App\Models\Inquiry\InquiryOccupation;
use App\Models\Inquiry\InquiryStatus;
use App\Models\Inquiry\VisitResult;
use App\Models\JobCard\TblJobCard;
use App\Models\Product;
use App\Services\BusinessService;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InquiryProgressAndFollowUpController extends Controller
{
    use CommonTrait,CodeGeneration;
    public function index(Request $request){
        $userId = Auth::user()->UserId;
        $export = $request->Export;

        $currentPage = $request->pagination['current_page'] ?? 1;
        $perPage = 20;

        if ($export == 'Y'){
            $currentPage = '%';
        }

        $sql = " exec usp_doLoadInquiryFollowUpAndProgressCard '$userId', '$perPage' ,'$currentPage' ";

        return $this->getReportData($sql, $perPage, $currentPage, $export);
    }

    public function getSupportingData(){
        $inquiryOccupation = InquiryOccupation::where('Active',1)->orderBy('SL','asc')->get();
        $inquiryCustomerCategory = InquiryCustomerCategory::where('Active',1)->get();
        $inquiryMainUser = InquiryMainUser::where('Active',1)->get();
        $inquiryMediaCategory = InquiryMediaCategory::where('Active',1)->get();
        $inquiryLevel = InquiryLevel::where('Active',1)->get();
        $inquiryDocumentCategory = InquiryDocumentCategory::where('Active',1)->get();
        $visitResults =  VisitResult::all();
        $competitorCompany =  CompetitorCompany::all();


        return response()->json([
            'inquiryOccupationSupportingData'=>$inquiryOccupation,
            'inquiryCustomerCategory'=>$inquiryCustomerCategory,
            'inquiryMainUser'=>$inquiryMainUser,
            'inquiryMediaCategory'=>$inquiryMediaCategory,
            'inquiryLevelSupportingData'=>$inquiryLevel,
            'inquiryDocumentCategory'=>$inquiryDocumentCategory,
            'visitResults'=>$visitResults,
            'competitorCompany'=>$competitorCompany,
        ]);
    }
    public function searchProduct($product){
        $allProduct = Product::select('ProductCode as id',DB::raw("CONCAT(ProductCode,'-',ProductName) AS ProductName"))
            ->where('Business','C')
            ->where(function ($q) use ($product){
                $q->where('ProductName', 'LIKE', '%'.$product . '%');
                $q->orWhere('ProductCode', 'LIKE', '%'.$product . '%');
            })
             ->where('Active','Y')->where('SMSOrder','Y')->get();
        return response()->json([
            'allProduct'=>$allProduct
        ]);
    }

    public function addProgressCard(Request $request){
        try{

            DB::beginTransaction();
            //Inquiry Master
            $inquiryMaster = new InquiryMaster();
            $inquiryMaster->CustomerName = $request->name;
            $inquiryMaster->ContactNo = $request->contactNo ;
            $inquiryMaster->ConvenientTimeToCall = $request->convenientTimeToCall;
            $inquiryMaster->Add1 = $request->address;
            $inquiryMaster->Age = $request->age;
            $inquiryMaster->Gender = $request->gender;
            $inquiryMaster->OccupationId = $request->occupation;
            $inquiryMaster->Current2Wheeler = $request->currentTwoWheeler;
            $inquiryMaster->CustomerCategoryId = $request->customerCategory;
            $inquiryMaster->InquiryMainUserId = $request->mainUserDetail;
            $inquiryMaster->MainUserOccupationId = $request->userOccupation;
            $inquiryMaster->UserCurrent2Wheeler = $request->userCurrentTwoWheeler;
            $inquiryMaster->ModelSuggested = $request->modelSuggested;
            $inquiryMaster->OfferTestRide = $request->testRiderOffer;
            $inquiryMaster->PurposeOfRoyal = $request->purposeOfRoyal;
            $inquiryMaster->AppralsAccessories = $request->appralsAccessories;
            $inquiryMaster->ProductCode = $request->product?$request->product['id']:'';
            $inquiryMaster->ModelYear = $request->modelYear;
            $inquiryMaster->ExpectedValue = $request->expectedValue;
            $inquiryMaster->BankScheme = $request->bankScheme;
            $inquiryMaster->InquiryLevelId = $request->inquiryLevel;
            $inquiryMaster->SlaesCunsultantName = $request->salesConsultantName;
            $inquiryMaster->InquiryRemark = $request->inquiryRemark;
            $inquiryMaster->EntryBy = Auth::user()->UserId;
            $inquiryMaster->EntryDate = Carbon::now();
            $inquiryMaster->EntryIpAddress = $request->ip();
            $inquiryMaster->save();
            //Inquiry Status
            $inquiryStatus=  new InquiryStatus();
            $inquiryStatus->InquiryId=  $inquiryMaster->InquiryId;
            $inquiryStatus->VisitResultId=  1;
            $inquiryStatus->ProductCode= $request->product? $request->product['id']:'';
            $inquiryStatus->ExpectedDelivery=  $request->expectedDelivery;
            $inquiryStatus->NextDelivery=  $request->nextVisit;
            $inquiryStatus->EntryBy=  Auth::user()->UserId;
            $inquiryStatus->EntryDate=  Carbon::now();
            $inquiryStatus->save();

            //Inquiry Media
            if(!empty($request->allMediaCategoryId)){
                foreach ($request->allMediaCategoryId as $singleId){
                    $inquiryMedia  = new InquiryMedia();
                    $inquiryMedia->InquiryId  =  $inquiryMaster->InquiryId;
                    $inquiryMedia->InquiryMediaCategoryId  =  $singleId;
                    $inquiryMedia->save();

                }
            }

            //Inquiry Document
            if(!empty($request->idAddressProof)){
                foreach ($request->idAddressProof as $singleAddressProf){
                    $inquiryDocument  = new InquiryDocument();
                    $inquiryDocument->InquiryId  =  $inquiryMaster->InquiryId;
                    $inquiryDocument->DocumentId  =  $singleAddressProf;
                    $inquiryDocument->save();

                }
            }
            if(!empty($request->residenceCum)){
                foreach ($request->residenceCum as $singleResidence){
                    $inquiryResidence  = new InquiryDocument();
                    $inquiryResidence->InquiryId  =  $inquiryMaster->InquiryId;
                    $inquiryResidence->DocumentId  =  $singleResidence;
                    $inquiryResidence->save();

                }
            }


            DB::commit();
            return response()->json([
                'status' => 'Success',
                'message' => 'Progress Card Added Successfully',
            ],200);

        }
        catch (\Exception $exception) {
          DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
    public function updateFollowUp(Request $request){
        try{
            $userId  = Auth::user()->UserId;
//            dd($request->product['id']);
            //check already exist or not
            $checkInquiry=  InquiryStatus::where('InquiryId',$request->inquiryId)->where('ProductCode',$request->product['id'])->first();


//            if($checkInquiry){
//                InquiryStatus::where('InquiryId',$request->inquiryId)->where('ProductCode',$request->product['id'])->update([
//                        'VisitResultId'=>$request->visitType,
//                        'CompetitorCompanyId'=>$request->competitorCompany,
//                        'ReceivedAmount'=>$request->receivedAmount,
//                        'BikeModel'=>$request->bikeModel,
//                        'ExpectedDelivery'=>$request->expectedDelivery,
//                        'NextDelivery'=>$request->nextVisit,
//                        'EntryBy'=>$userId,
//                        'EntryDate'=>Carbon::now(),
//                ]);
//            }
//            else{

                $inquiryStatus = new InquiryStatus();
                $inquiryStatus->InquiryId=$request->inquiryId;
                $inquiryStatus->VisitResultId=$request->visitType;
                $inquiryStatus->ProductCode=$request->product? $request->product['id']:'';
                $inquiryStatus->ExpectedDelivery=$request->expectedDelivery;
//                $inquiryStatus->DeliveryPriority=$request
                $inquiryStatus->NextDelivery=$request->nextVisit;
                $inquiryStatus->CompetitorCompanyId=$request->competitorCompanies;
                $inquiryStatus->BikeModel=$request->bikeModel;
                $inquiryStatus->ReceivedAmount=$request->receivedAmount;
                $inquiryStatus->EntryBy=$userId;
                $inquiryStatus->EntryDate=Carbon::now();
                $inquiryStatus->save();
//            }

            return response()->json([
                'status' => 'Success',
                'message' => "Successfully updated"
            ]);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }



}
