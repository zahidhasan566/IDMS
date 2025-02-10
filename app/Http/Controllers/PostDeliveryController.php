<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDetails;
use App\Models\DeliveryMaster;
use App\Models\PostDelivery;
use App\Services\PostDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        $query = DeliveryMaster::query();
        if (!empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('CustomerName','like','%'.$request->search.'%');
                $q->orWhere('CustomerMobile','like','%'.$request->search.'%');
                $q->orWhere('FrameNo','like','%'.$request->search.'%');
            });
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' =>$query->orderBy('InquiryId')
                    ->select('InquiryId','CustomerName','CustomerMobile','FrameNo','PrintCount')
                    ->get(),
            ]);
        } else {
            return $query->orderBy('InquiryId')
                ->select('InquiryId','CustomerName','CustomerMobile','FrameNo','PrintCount')
                ->paginate($take);
        }
    }

    public function create()
    {
        return response()->json([
            'data' => PostDelivery::where('Active','Y')
                ->select(DB::raw("PostDeliveryChecklist.*,'Y' as Status,'' as Remarks"))
                ->orderBy('ChecklistId')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerName' => 'required|string',
            'customerMobile' => 'required|string',
            'chassisNo' => 'required|string',
            'fields' => 'required'
        ]);
        DB::beginTransaction();
        try {
            //LOGIC CHECK
            if ((new PostDeliveryService())->uniqueCheckMobile($request->customerMobile)) {
                return response()->json([
                    'message' => 'Checklist already created with this mobile number'
                ],409);
            }
            if ((new PostDeliveryService())->uniqueCheckChassis($request->chassisNo)) {
                return response()->json([
                    'message' => 'Checklist already created with this chassis number'
                ],409);
            }
            $userID = Auth::user()->UserId;
            //INSERTION MASTER DATA
            $master = DeliveryMaster::create([
                'CustomerName' => $request->customerName,
                'CustomerMobile' => $request->customerMobile,
                'FrameNo' => $request->chassisNo,
                'DealerCode' => $userID
            ]);
            //INSERTION DETAILS DATA
            if (count($request->fields)) {
                foreach ($request->fields as $field) {
                    DeliveryDetails::create([
                        'InquiryId' => $master->InquiryId,
                        'ChecklistId' => $field['ChecklistId'],
                        'Status' => $field['Status'],
                        'Remarks' => empty($field['Remarks']) ? '' : $field['Remarks']
                    ]);
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Records mismatch!'
                ],500);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Checklist created successfully.',
                'inquiryId' => $master->InquiryId
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }

    public function printData($inquiryId)
    {
        $master = DeliveryMaster::where('InquiryId',$inquiryId)->first();
        //update print count
        DeliveryMaster::where('InquiryId',$inquiryId)->update([
            'PrintCount' => intval($master->PrintCount) + 1
        ]);
        $details = DB::table('DeliveryChecklistDetails','dd')
            ->join('PostDeliveryChecklist as p','p.ChecklistId','dd.ChecklistId')
            ->where('dd.InquiryId',$inquiryId)
            ->select('p.ChecklistId','p.InspectionAreas','p.Inquiry','dd.Status','dd.Remarks')
            ->orderBy('p.ChecklistId')
            ->get();
        return response()->json([
            'master' => $master,
            'details' => $details
        ]);
    }
}
