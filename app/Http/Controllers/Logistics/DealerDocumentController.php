<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DealearInvoiceDocument;
use App\Models\Logistics\DealerDocument;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use PhpParser\Node\Expr\New_;

class DealerDocumentController extends Controller
{
    use CommonTrait;
    public function index(Request $request){
        $roleId = Auth::user()->RoleId;
        $take = $request->take;
        $search = $request->search;

        $list = DealerDocument::select(
            'DocumentID',
            'DocumentSLNo',
            'DocumentFileName',
            'EntryBy',
            'EntryDate'
        )
            ->where(function ($q) use ($search) {
                $q->where('DocumentID', 'like', '%' . $search . '%');
                $q->Orwhere('DocumentSLNo', 'like', '%' . $search . '%');
            })
            ->orderBy('EntryDate','desc');

        if ($request->type === 'export') {
            return response()->json([
                'data' => $list->get(),
            ]);
        } else {
            return $list->paginate($take);
        }
    }

    public function store(Request $request)
    {
        if($request->hasfile('files'))
        {
            foreach($request->file('files') as $file)
            {

                $filename =$file->getClientOriginalName();
                $filePath = public_path('uploads/dealerdocument/');
                $file->move($filePath, $filename);
                $docNo=explode('.',$filename,-1);
                $existDealer = DealerDocument::where('DocumentSLNo','=',$docNo[0])->first();
                if (empty($existDealer)){
                    $dealer = new DealerDocument();
                    $dealer->DocumentFileName = $filename;
                    $dealer->DocumentSLNo = $docNo[0];
                    $dealer->EntryBy = Auth::user()->UserId;
                    $dealer->EntryDate = Carbon::now();
                    $dealer->EntryIpAddress = \request()->ip();
                    $dealer->save();

                }
                $existDealer->DocumentFileName = $filename;
                $existDealer->DocumentSLNo = $docNo[0];
                $existDealer->EditedBy = Auth::user()->UserId;
                $existDealer->EditedDate = Carbon::now();
                $existDealer->EditedIpAddress = \request()->ip();
                $existDealer->save();

            }
            return response()->json([
                'status' => 'success',
                'message'=>'Successfully Stored'
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message'=>'Upload Problem'
            ]);
        }
    }

    public function getInvoiceDetails(Request $request){

        $invoiceno = $request->InvoiceNo;
        $document = $this->loadInvoiceDetails($invoiceno);
        return response()->json([
            'data'=>$document,
            'status' => 'success',
        ]);
    }

    public function storeDocument(Request $request)
    {
        $allData      = $request->allData;
        $sendDate     = $request->sendDate;
        $customerCode = $request->customerCode;
        $userID       = Auth::user()->UserId;
        $ipAddress    = \request()->ip();
        $customerdata = $this->allCustomer($customerCode);
        $mobileNo     = $customerdata[0]->Mobile ? $customerdata[0]->Mobile : $customerdata[0]->Phone;

        if(!empty($allData)){
            $message = "Dear Concern,%0D%0AMotorcycle registration document already has been sent. Please check and entry in YAMAHA-DMS after receiving from courier. %0D%0AThanks, %0D%0ALogistic Team(Yamaha)%0D%0AACI Motors Ltd.";
           $smsStatus = $this->sendSmsQ(
               $mobileNo, 8809617614917, 'Yamaha-DMS', 'Logistics Document',
                'Yamaha',$userID, 'smsq', $message
            );
           $res =json_decode($smsStatus ,true);
           foreach ($allData as $key){
            $invoiceNo  = $key["invoiceno"];
               $customerCode    = $key["customercode"];
               $chassisno       = $key["chassisno"];
               $engineno        = $key["engineno"];
               $productcode     = $key["productcode"];
               $entryby         = $userID;

               $document = DealearInvoiceDocument::where('Invoiceno',$invoiceNo)->where('ChassisNo',$chassisno)->first();

               if ($document){
                   $document->EngineNo       =      $engineno;
                   $document->EditDate       =      $sendDate;
                   $document->EditBy         =      $userID;
                   $document->EditIPAddress  =      $ipAddress;
                   $document->EditIpAddress  =      $ipAddress;
                   $document->save();
               }else{
                   $document =new DealearInvoiceDocument();
                   $document->InvoiceNo      =      $invoiceNo;
                   $document->CustomerCode   =      $customerCode;
                   $document->ChassisNo      =      $chassisno;
                   $document->EngineNo       =      $engineno;
                   $document->ProductCode    =      $productcode;
                   $document->SendDate       =      $sendDate;
                   $document->SendBy         =      $userID;
                   $document->SendIPAddress  =      $ipAddress;
                   $document->save();
               }


           }
           foreach ($allData as $key){
               $invoiceNo       =    $key["invoiceno"];
               $chassisno       =    $key["chassisno"];
               $document = DealearInvoiceDocument::where('Invoiceno',$invoiceNo)->where('ChassisNo',$chassisno)->first();
               $document->InsertedSmsIds      =      $res['data']['apiStatusCode'];
               $document->SMSMessage          =      $res['data']['status'];
               $document->save();
           }
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully added your document',
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No document selected',
        ]);
    }


}
