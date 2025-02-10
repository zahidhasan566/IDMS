<?php

namespace App\Http\Controllers\Logistics;

use App\Models\Customer;
use App\Traits\CommonTrait;
use App\Models\LostDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LostDocumentDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Logistics\DealearInvoiceDocument;

class LostDocumentController extends Controller
{

    use CommonTrait;

        // Modify the query to use paginate
    public function getLostInvoice(Request $request) {
        $status = $request->input('status');
        $export = $request->input('export');
    
        $currentPage = $request->input('pagination.current_page') ?? 1;
        $perPage = 20;
    
        // Check for valid status values
        if (!in_array($status, ['Y', 'N'])) {
            return response()->json(['error' => 'Invalid status parameter'], 400);
        }
    
        $query = DB::table('LostDocument as LD')
            ->join('LostDocumentDetails as LDD', 'LD.LostDocumentID', '=', 'LDD.LostDocumentID')
            ->join('UserManager as UM', 'UM.UserId', '=', 'LD.EntryBy')
            ->where('LDD.Approved', $status)
            ->orderByDesc('LDD.LostDocumentID')
            ->select('LD.*', 'LDD.*', 'UM.*'); 
        
        if ($export != 'Y') {
            $dataList = $query->paginate($perPage, ['*'], 'page', $currentPage);
            $paginationData = [
                'current_page' => $dataList->currentPage(),
                'last_page' => $dataList->lastPage(),
                'total' => $dataList->total(),
                'from' => $dataList->firstItem(),
                'to' => $dataList->lastItem(),
            ];
            $dataList = json_decode(json_encode($dataList->items()), true);
        } else {
            $dataList = $query->get(); 
            $numberOfRecord = $dataList->count();
            $paginationData = [
                'current_page' => 1,
                'last_page' => 1,
                'total' => $numberOfRecord,
                'from' => 1,
                'to' => $numberOfRecord,
            ];
        }

        return response()->json([
            'data' => generateDownloadFileLink($dataList),
            'paginationData' => $paginationData
        ]);
    }
        

    public function updateLostInvoiceStatus(Request $request) {
        $selectedItems = $request->selectedItems;
        $status = $request->status;
        $userId = Auth::id();
        $approveDate = date('d M Y');
        $ipAddress = $request->ip();

        try {
            DB::beginTransaction();
            foreach ($selectedItems as $engineNo) {
                LostDocumentDetails::where('EngineNo', $engineNo)
                    ->update([
                        'Approved' => $status,
                        'ApprovedBy' => $userId,
                        'ApprovedDate' => $approveDate,
                        'ApprovedIpAddress' => $ipAddress,
                    ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Document Status update successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update document status'
            ], 500);
        }

    }

    public function sendLostInvoiceCopy(Request $request) {
        $userId = Auth::id();
        $sendDocumentDate= date('d M Y');
        $selectedItems = $request->selectedItems;
        $ipAddress = $request->ip();

        try {
            foreach ($selectedItems as $engineNo) {
                LostDocumentDetails::where('EngineNo',$engineNo)
                ->update([
                    'SendDocument' => 'Y',
                    'SendDocumentBy' => $userId,
                    'SendDocumentDate' => $sendDocumentDate,
                    'SendDocumentIpAddress' => $ipAddress,
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Document send successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send document'
            ], 500);
        }

    }

    public function getLostInvoiceDetails(Request $request) {
        $invoiceNo = $request->input('invoiceNo');
    
        try {
            $data = [
                'invoiceData' => $this->fetchInvoiceDetails($invoiceNo),
                'countData' => DealearInvoiceDocument::where('Invoiceno', $invoiceNo)->count()
            ];
    
            return response()->json($data);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function storeLostInvoiceDetails(Request $request) {
        $validator = Validator::make($request->all(), [
            'invoiceNo' => 'required|string',
            'selectedItems' => 'required|string',
            'lostDocumentReason' => 'required|string',
            'gdCopy' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'deliveryChalan' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'othersDocument' => 'required|file|mimes:jpeg,png,pdf|max:2048'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $userId = Auth::id();
        $ipAddress = $request->ip();
        $invoiceNo = $request->input('invoiceNo');
        $customerCode = Auth::id();
        $selectedItems = json_decode($request->input('selectedItems'));
        $lostDocumentReason = $request->input('lostDocumentReason');
    
        
        DB::beginTransaction();
        
        try {
            foreach ($selectedItems as $item) {
                $chassisNo = $item->chassisno;
                $existingDocument = LostDocumentDetails::where('ChassisNo', $chassisNo)
                ->orderBy('LostDocumentID', 'DESC')
                ->first();
                
                if ($existingDocument && $existingDocument->Approved === 'N') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Requisition Item already Pending'
                    ], 400);
                }
            }
            $gdCopyPath = $request->hasFile('gdCopy') ? storeRenamedFile($request->file('gdCopy'), 'gdCopy_') : null;
            $deliveryChalanPath = $request->hasFile('deliveryChalan') ? storeRenamedFile($request->file('deliveryChalan'), 'deliveryChalan_') : null;
            $othersDocumentPath = $request->hasFile('othersDocument') ? storeRenamedFile($request->file('othersDocument'), 'othersDocument_') : null;
            
            $dataLostDocument = [
                'InvoiceNo' => $invoiceNo,
                'CustomerCode' => $customerCode,
                'InvoiceDate' => now(),
                'GDCopy' => $gdCopyPath,
                'DeliveryChalan' => $deliveryChalanPath,
                'OtherDocument' => $othersDocumentPath,
                'EntryBy' => $userId,
                'EntryDate' => now(),
                'EntryIPAddress' => $ipAddress,
                'EntryDiviceState' => getDeviceState(),
            ];
    
            $lostDocument = LostDocument::create($dataLostDocument);
            $lostDocumentId = $lostDocument->LostDocumentID;
    
            foreach ($selectedItems as $item) {
                LostDocumentDetails::create([
                    'LostDocumentID' => $lostDocumentId,
                    'EngineNo' => $item->engineno ?? null,
                    'ChassisNo' => $item->chassisno ?? null,
                    'ProductCode' => $item->productcode ?? null,
                    'LostDocumentReason' => $lostDocumentReason,
                    'Approved' => "N",
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Lost document stored successfully'
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => 'error',
                'message' => 'Error storing lost document: ' . $e->getMessage()
            ], 500);
        }
    }
 
    private function fetchInvoiceDetails($invoiceNo) {
        $sql = "exec doLoadInvoiceDetailsLostDocument :invoiceNo";
        $conn = DB::connection('sqlsrvread');
        $stmt = $conn->getPdo()->prepare($sql);
        $stmt->bindParam(':invoiceNo', $invoiceNo, \PDO::PARAM_STR);
        $stmt->execute();
    
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function customerList() {
        $user = Auth::user();
        $customerCodeFilter = $user->grpUser == 1 ? '%' : $user->userid;
    
        // Build the query using Eloquent
        $customers = Customer::where('CustomerCode', 'LIKE', $customerCodeFilter)
//                              ->whereIn('CustomerType', ['E', 'D', 'R'])
//                              ->whereRaw('LEFT(CustomerCode, 2) = ?', ['HC'])
                              ->get();
        
        return $customers;
    }

    public function lostDocument(Request $request) {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $chassisNo = $request->chassisNo;
        $invoiceNo = $request->invoiceNo;
        $reportType = $request->reportType;
        $userId = Auth::user()->UserId;
        $export = $request->Export;

        $currentPage = $request->pagination['current_page'] ?? 1;
        $perPage = 20;

        if ($export == 'Y'){
            $currentPage = '%';
        }

        $sql = " exec usp_doLoadLogisticsLostDocumentReport'$dateFrom', '$dateTo', '$userId', '$chassisNo','$invoiceNo','$reportType','$perPage','$currentPage' ";
        
        return $this->getReportData($sql, $perPage, $currentPage, $export);
    } 
}
