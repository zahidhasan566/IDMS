<?php

namespace App\Http\Controllers\iHelpBD;

use App\Models\JobCard\TblJobCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SendCallRequestController extends Controller
{
    //
    public function sendYamahaCall(Request $request) {
        try {
            // Log request information
            Log::channel('ihelpcall')->info('User ID: ' . auth()->user()->UserId . ' -- ' . now());
 
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');
            $requestType = $request->input('requestType');

            if ($requestType === 'sales') {
                $firstQuery = DB::table('DealarInvoiceMaster as M')
                    ->select([
                        'M.MobileNo as mob_no',
                        DB::raw("CONVERT(VARCHAR(50), M.InvoiceID) as invoice_no"),
                        'M.InvoiceDate as invoice_date',
                        'M.CustomerName as customer_name',
                        'D.ChassisNo as chassis_no',
                        'C.CustomerName as dealer_name',
                        'C.CustomerCode as dealer_code'
                    ])
                    ->join('DealarInvoiceDetails as D', 'M.InvoiceID', '=', 'D.InvoiceID')
                    ->join('Customer as C', 'C.CustomerCode', '=', 'M.MasterCode')
                    ->whereBetween('M.InvoiceDate', [$dateFrom, $dateTo])
                    ->whereRaw('LEN(D.ChassisNo) > 5');
                
                $secondQuery = DB::table('Invoice as i')
                    ->select([
                        'c.Mobile as mob_no',
                        'i.InvoiceNo as invoice_no',
                        'i.InvoiceDate as invoice_date',
                        'c.CustomerName as customer_name',
                        'idb.BatchNo as chassis_no', // Ensure alias is the same as in the first query
                        'c.CustomerName as dealer_name',
                        'c.CustomerCode as dealer_code'
                    ])
                    ->join('Customer as c', 'i.CustomerCode', '=', 'c.CustomerCode')
                    ->join('InvoiceDetails as id', 'i.InvoiceNo', '=', 'id.Invoiceno')
                    ->join('InvoiceDetailsBatch as idb', function ($join) {
                        $join->on('id.Invoiceno', '=', 'idb.Invoiceno')
                             ->on('id.ProductCode', '=', 'idb.ProductCode');
                    })
                    ->whereIn('i.DepotCode', ['S', 'M'])
                    ->whereBetween('i.InvoiceDate', [$dateFrom, $dateTo])
                    ->where('i.Business', '=', 'C')
                    ->where('i.Returned', '=', 'N');
                
                    $invoices = $firstQuery->unionAll($secondQuery)->get();
                

            } else {
                $invoices = DB::table('tblJobCard AS j')
                            ->select([
                                'j.JobCardNo AS job_card_no',
                                'j.MobileNo AS mob_no',
                                'j.JobDate AS job_date',
                                'j.CustomerName AS customer_name',
                                'j.ChassisNo AS chassis_no',
                                'c.CustomerName AS service_dealer',
                                'j.ServiceCenterCode AS service_dealer_code'
                            ])
                            ->join('Customer AS c', 'j.ServiceCenterCode', '=', 'c.CustomerCode')
                            ->whereBetween('j.JobDate', [$dateFrom, $dateTo])
                            ->get();
            }

            $totalData = count($invoices);
            $chunkData = $invoices->chunk(20)->toArray();
            $result = $this->sendYamahaCallRequest($chunkData, $totalData, $requestType);
            //dd($result);
 
            $statusCode = $result['status'] === 'success' ? 200 : 500;
            return response()->json(["message" => $result['message']], $statusCode);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['error' => $th->getMessage()], 500);
        }
  
    }

    public function sendYamahaCallRequest($chunkData, $totalData, $requestType)
    {
        $totalChunkCount = 0;
        try{
            foreach ($chunkData as $singleChunkData) {
                $url = ($requestType === 'sales')
                    ? 'http://103.143.148.66/API_V11/new_create.php'
                    : 'http://103.143.148.66/API_V11/create.php';

                // Send the HTTP POST request
                $response = Http::withHeaders([
                    'Authorization' => '@ACI-iHelp@',
                    'Content-Type' => 'application/json',
                ])->post($url, $singleChunkData);

                $data = $response->json();
                $successNumber = $data['data']['success'] ?? 0;
                $apiStatus = $data['status'] ?? null;

                if ($apiStatus == 200 && $successNumber) {
                    foreach ($singleChunkData as $singleData) {
                        //dd($singleData);
                        $totalChunkCount++;

                        if ($requestType === 'sales') {
                            $invoice_id = intval($singleData->invoice_no);
                            $sql = "update DealarInvoiceMaster set isSync =1 where InvoiceID=$invoice_id";

                            $conn = DB::connection('sqlsrv');
                            $pdo = $conn->getPdo()->prepare($sql);
                            $pdo->execute();

//                            DB::table('DealarInvoiceMaster')
//                                ->where('InvoiceID', $invoice_id)
//                                ->update(['isSync' => 1]);
                        } else {
                            $jobCardNo = $singleData->job_card_no;
                            $sql = "update tblJobCard set isSync =1 where JobCardNo='$jobCardNo'";
                            $conn = DB::connection('sqlsrv');
                            $pdo = $conn->getPdo()->prepare($sql);
                            $pdo->execute();
//                            $updateJobCard = TblJobCard::where('JobCardNo', $jobCardNo)->first();
//                            $updateJobCard->isSync =  1;
//                            $updateJobCard->save();
                        }


                    }
                }
            }
            return $this->generateSuccessMessage($totalChunkCount, $totalData);
        }
        catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function sendFotonCall(Request $request) {
        
        try {
            // Retrieve input data
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo'); 
    
            // Use the desired database connection
            $data = DB::connection('foton')
                ->table('tblJobCard as tj')
                ->join('tblTechnicianSetup as techst', 'techst.TechnicianCode', '=', 'tj.TechnicianCode')
                ->select([
                    'tj.MobileNo as mob_no',
                    'tj.DriverContactNo as driver_no',
                    'tj.JobCardNo as job_card_no',
                    'tj.JobDate as job_date',
                    'tj.CustomerName as customer_name',
                    'tj.DriverName as driver_name',
                    'tj.Address as customer_address',
                    'tj.ChassisNo as chassis_no',
                    'techst.TechnicianName as technician_name',
                    'tj.TechnicianCode as staff_id'
                ])
                ->whereBetween('tj.JobDate', [$dateFrom, $dateTo])
                ->where('tj.MobileNo', '<>', '')
                ->where('tj.DriverContactNo', '<>', '')
                ->where('tj.JobCardNo', '<>', '')
                ->where('tj.JobDate', '<>', '')
                ->where('tj.CustomerName', '<>', '')
                ->where('tj.DriverName', '<>', '')
                ->where('tj.Address', '<>', '')
                ->where('tj.ChassisNo', '<>', '')
                ->where('techst.TechnicianName', '<>', '')
                ->where('tj.TechnicianCode', '<>', '')
                ->get();
            if ($data->isNotEmpty()) {
                $totalData = $data->count();
                $chunkData = $data->chunk(20)->toArray();
                $result = $this->sendFotonCallRequest($chunkData, $totalData);
    
                $statusCode = $result['status'] === 'success' ? 200 : 500;
                return response()->json(["message" => $result['message']], $statusCode);
            }
            return response()->json(["message" =>'No data found'], 404);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function sendFotonCallRequest($chunkData, $totalData)
    { 
        $totalChunkCount = 0; 
        try {
            foreach ($chunkData as $singleChunkData) {
                if (!empty($singleChunkData)) {
                    // Send the HTTP POST request
                    $response = Http::withHeaders([
                        'Authorization' => '@ACI-iHelp@',
                        'Content-Type' => 'application/json',
                    ])->post('http://aci.ihelpbd.com/API_V11/foton_create.php', $singleChunkData);

                    $data = $response->json();

                    $successNumber = $data['data']['success'] ?? 0;
                    $apiStatus = $data['status'] ?? null;

                    if ($apiStatus == 200 && $successNumber) {
                        foreach ($singleChunkData as $singleData) {
                            $totalChunkCount++;

                            $job_card_no = $singleData->job_card_no;

                            $sql = "update tblJobCard set isSync =1 where JobCardNo='$job_card_no'";
                            $conn = DB::connection('foton');
                            $pdo = $conn->getPdo()->prepare($sql); 
                            $pdo->execute();
                        }
                    }
                }
            } 
            return $this->generateSuccessMessage($totalChunkCount, $totalData);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function sendAgrimotorsCall(Request $request) 
    {
        try {
            Log::channel('ihelpcall')->info('User ID: ' . auth()->user()->UserId . ' -- ' . now());
 
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');
            $requestType = $request->input('requestType'); 

            $data = DB::connection('motor_service_auto')->table('job_cards as jc')
                ->select(
                    'jc.customer_moblie as customer_phone',
                    'ar.name as area',
                    'jc.job_card_no as jobcard_no',
                    'engnr.name as engineer_name',
                    'jc.customer_name',
                    'jc.chassis_number as chassis_no',
                    'tchn.name as technician_name',
                    'tchn.username as technitian_username',
                    'jc.product_type as product',
                    'cltype.name as call_type',
                    'srtyp.name as service_type',
                    'prtcp.name as participant',
                    DB::raw('0 as is_six'),
                    'jc.service_date'
                )
                ->leftJoin('areas as ar', 'ar.id', '=', 'jc.area_id')
                ->leftJoin('users as engnr', 'engnr.id', '=', 'jc.engineer_id')
                ->leftJoin('users as tchn', 'tchn.id', '=', 'jc.technitian_id')
                ->leftJoin('call_types as cltype', 'cltype.id', '=', 'jc.call_type_id')
                ->leftJoin('service_types as srtyp', 'srtyp.id', '=', 'jc.service_type_id')
                ->leftJoin('users as prtcp', 'prtcp.id', '=', 'jc.participant_id')
                ->where('jc.product_type', $requestType)
                ->whereNotNull('jc.job_card_no')
                ->whereNotNull('jc.customer_moblie')
                ->whereBetween('jc.service_date', [$dateFrom, $dateTo])
                ->orderByDesc('jc.id')
                ->get();

            if ($data->isNotEmpty()) {
                $totalData = $data->count();
                $chunkData = $data->chunk(20)->toArray();
                $result = $this->sendAgrimotorsCallRequest($chunkData, $totalData, $requestType);
    
                $statusCode = $result['status'] === 'success' ? 200 : 500;
                return response()->json(["message" => $result['message']], $statusCode);
            }
    
            return response()->json(["message" => 'No data found'], 404);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function sendAgrimotorsCallRequest($chunkData, $totalData, $requestType)
    { 
        $totalChunkCount = 0;

        foreach ($chunkData as $singleChunkData) {
            $url = $requestType === 'Tractor'
                ? 'http://aci.ihelpbd.com/API_V11/tractor_create.php'
                : 'http://aci.ihelpbd.com/API_V11/harvester_create.php';

            $response = Http::withHeaders([
                'Authorization' => '@ACI-iHelp@',
                'Content-Type' => 'application/json',
            ])->post($url, $singleChunkData);

            $data = $response->json();
            if ($response->status() == 200 && $data['data']['success']) {
                foreach ($singleChunkData as $singleData) {
                    $totalChunkCount++;
                    $jobCardNo = $singleData->jobcard_no; 
                   
                    $sql = "update job_cards set isSync =1 where job_card_no='$jobCardNo'";
                 
                    $conn = DB::connection('motor_service_auto');
                    $pdo = $conn->getPdo()->prepare($sql); 
                    $pdo->execute();
                }
            }
        } 
        return $this->generateSuccessMessage($totalChunkCount, $totalData);
    }

    public function generateSuccessMessage($totalChunkCount, $totalData) {
        if ($totalChunkCount === $totalData) {
            return [
                'status' => 'success',
                'message' => "success: $totalChunkCount of $totalData requests completed",
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "Incomplete Total Request: $totalChunkCount of $totalData requests completed",
            ];
        }
    }
}
