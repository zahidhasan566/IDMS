<?php

namespace App\Http\Controllers;

use App\Models\DealarWarrantyClaim;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ClaimWarrantyController extends Controller
{
    use CommonTrait;

    public function getAllClaimWarranty(Request $request)
    {
        $ChassisNo = $request->ChassisNo;
        $MasterCode = Auth::user()->UserId;

        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;

        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo . ' 23:59:59.000';
        $DateTo = date('Y-m-d H:i:s', strtotime($DateTo));

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }

        $sql = "EXEC usp_LoadWarrantyClaim '$MasterCode','$DateFrom','$DateTo','$ChassisNo','$PerPage','$CurrentPage'";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $MasterCode = Auth::user()->UserId;

            $DealarWarrantyClaim = new DealarWarrantyClaim();
            $DealarWarrantyClaim->MasterCode = $MasterCode;
            $DealarWarrantyClaim->JobCardNo = $request->JobCardNo;
            $DealarWarrantyClaim->WCDate = date('Y-m-d H:i:s', strtotime("now"));
            $DealarWarrantyClaim->ChassisNo = $request->ChassisNo;
            $DealarWarrantyClaim->ProductCode = '';
            $DealarWarrantyClaim->Mileage = $request->Mileage;
            $DealarWarrantyClaim->ProblemDetails = $request->ProblemName;
            $DealarWarrantyClaim->OccuranceDate = $request->OccuranceDate;
            $DealarWarrantyClaim->WarrantyTypeId = $request->TypeOfWarranty;
            $DealarWarrantyClaim->WarrantySourceId = $request->SourceOfInformation;
            $DealarWarrantyClaim->WarrantySeriousnessId = $request->Seriousness;
            $DealarWarrantyClaim->TechnicianName = $request->TechnicianName;
            $DealarWarrantyClaim->RiderSex = $request->Sex ? $request->Sex : '';
            $DealarWarrantyClaim->RiderAge = $request->Age;
            $DealarWarrantyClaim->RiderWeight = $request->Weight ? $request->Weight : '';
            $DealarWarrantyClaim->RidingStyle = $request->RidingStyle ? $request->RidingStyle : '';
            $DealarWarrantyClaim->RoadCondition = $request->RoadCondition ? $request->RoadCondition : '';
            $DealarWarrantyClaim->CustomerComment = $request->CustomerComments ? $request->CustomerComments : '';
            $DealarWarrantyClaim->DiagnosisFailureAnalysis = $request->FailureAnalysis ? $request->FailureAnalysis : '';
            $DealarWarrantyClaim->RemedyResult = $request->RemedyResult ? $request->RemedyResult : '';
            $DealarWarrantyClaim->SuspectedCauseOfFailure = $request->CauseOfFailure ? $request->CauseOfFailure : '';
            $DealarWarrantyClaim->FreeServiceSchedule = $request->ServiceSchedule ? $request->ServiceSchedule : '';
            $DealarWarrantyClaim->AdditionalComments = $request->AdditionalComments ? $request->AdditionalComments : '';
            $DealarWarrantyClaim->WarrantyProblemIsId = $request->ProblemIs ? $request->ProblemIs : '';
            $DealarWarrantyClaim->WarrantyRemedyId = $request->Remedy ? $request->Remedy : '';
            $DealarWarrantyClaim->WarrantyProblemResultId = $request->Result ? $request->Result : '';
            $DealarWarrantyClaim->OccupationId = $request->RiderProfession ? $request->RiderProfession : '';
            $DealarWarrantyClaim->save();

            //FOR IMAGE
            if ($request->Picture) {
                foreach ($request->Picture as $image) {
                    $image = $image['url'];
                    $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                    Image::make($image)->save(public_path('assets/images/claim_warranty/') . $name);

                    DB::table('DealarWarrantyClaimDetails')->insert([
                        'DCWarrantyID' => $DealarWarrantyClaim->DCWarrantyID,
                        'ProImageThumb' => '',
                        'ProImagePath' => $name,
                    ]);
                }
            }

            $fieldsData = $request->fieldsData;
            foreach ($fieldsData as $item) {
                DB::table('DealarWarrantyClaimProduct')->insert([
                    'DCWarrantyID' => $DealarWarrantyClaim->DCWarrantyID,
                    'WarrantyInvoiceId' => $item['InvoiceType'],
                    'ProductCode' => $item['ItemCode'],
                    'Quantity' => $item['Quantity'],
                    'ServiceCharge' => $item['serviceCharge'],
                    'UnitPrice' => $item['UnitPrice'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Created'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function update(Request $request)
    {

        try {
            DB::beginTransaction();

            $MasterCode = Auth::user()->UserId;

            $DealarWarrantyClaim = DealarWarrantyClaim::query()->where('DCWarrantyId', $request->WarrantyId)->first();
            $DealarWarrantyClaim->ChassisNo = $request->ChassisNo;
            $DealarWarrantyClaim->Mileage = $request->Mileage;
            $DealarWarrantyClaim->ProblemDetails = $request->ProblemName;
            $DealarWarrantyClaim->OccuranceDate = $request->OccuranceDate;
            $DealarWarrantyClaim->WarrantyTypeId = $request->TypeOfWarranty;
            $DealarWarrantyClaim->WarrantySourceId = $request->SourceOfInformation;
            $DealarWarrantyClaim->WarrantySeriousnessId = $request->Seriousness;
            $DealarWarrantyClaim->TechnicianName = $request->TechnicianName;
            $DealarWarrantyClaim->RiderSex = $request->Sex ? $request->Sex : '';
            $DealarWarrantyClaim->RiderAge = $request->Age;
            $DealarWarrantyClaim->RiderWeight = $request->Weight ? $request->Weight : '';
            $DealarWarrantyClaim->RidingStyle = $request->RidingStyle ? $request->RidingStyle : '';
            $DealarWarrantyClaim->RoadCondition = $request->RoadCondition ? $request->RoadCondition : '';
            $DealarWarrantyClaim->CustomerComment = $request->CustomerComments ? $request->CustomerComments : '';
            $DealarWarrantyClaim->DiagnosisFailureAnalysis = $request->FailureAnalysis ? $request->FailureAnalysis : '';
            $DealarWarrantyClaim->RemedyResult = $request->RemedyResult ? $request->RemedyResult : '';
            $DealarWarrantyClaim->SuspectedCauseOfFailure = $request->CauseOfFailure ? $request->CauseOfFailure : '';
            $DealarWarrantyClaim->FreeServiceSchedule = $request->ServiceSchedule ? $request->ServiceSchedule : '';
            $DealarWarrantyClaim->AdditionalComments = $request->AdditionalComments ? $request->AdditionalComments : '';
            $DealarWarrantyClaim->WarrantyProblemIsId = $request->ProblemIs ? $request->ProblemIs : '';
            $DealarWarrantyClaim->WarrantyRemedyId = $request->Remedy ? $request->Remedy : '';
            $DealarWarrantyClaim->WarrantyProblemResultId = $request->Result ? $request->Result : '';
            $DealarWarrantyClaim->OccupationId = $request->RiderProfession ? $request->RiderProfession : '';
            $DealarWarrantyClaim->ApproveBy = $MasterCode;
            $DealarWarrantyClaim->ApproveDate = Carbon::now();
            $DealarWarrantyClaim->Status = 1;
            $DealarWarrantyClaim->save();

            //DEALER WARRANTY CLAIM PRODUCT 51468
            DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyId', $request->WarrantyId)->delete();
            $fieldsData = $request->fieldsData;
            foreach ($fieldsData as $item) {
                DB::table('DealarWarrantyClaimProduct')->insert([
                    'DCWarrantyID' => $DealarWarrantyClaim->DCWarrantyID,
                    'WarrantyInvoiceId' => $item['InvoiceType'],
                    'ProductCode' => $item['ItemCode'],
                    'Quantity' => $item['Quantity'],
                    'ServiceCharge' => $item['serviceCharge'],
                    'UnitPrice' => $item['UnitPrice'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Updated'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    public function getWarrentyFirstTime()
    {

        $conn = DB::connection('sqlsrv');

        $sql = "exec usp_doLoadWarrentyFirstTime";
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        $data = [];
        $data['WarrantyType'] = $res[0];
        $data['WarrantySource'] = $res[1];
        $data['WarrantySeriousness'] = $res[2];
        $data['WarrantyInvoiceType'] = $res[3];
        $data['ServiceSchedule'] = $res[4];
        $data['WarrantyProblemIs'] = $res[5];
        $data['WarrantyRemedy'] = $res[6];
        $data['WarrantyProblemResult'] = $res[7];
        $data['Occupation'] = $res[8];

        return response()->json([
            'warranty' => $data,
        ]);
    }

    public function jobCardWiseInfo(Request $request)
    {

        try {
            $JobCardNo = $request->JobCardNo;

            $conn = DB::connection('sqlsrv');
            $sql = "exec usp_doLoadJobCardDetils '$JobCardNo'";
            $pdo = $conn->getPdo()->prepare($sql);
            $pdo->execute();
            $res = array();
            do {
                $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
                $res[] = $rows;
            } while ($pdo->nextRowset());

            if (empty($res[0])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Job Card Not Found',
                ], 200);
            } else {
                $data = [];
                $data['JobCardDetails'] = $res[0];
                $data['PartsDetails'] = $res[1];
                $data['WorkDetails'] = $res[2];
                $data['ChassisNo'] = $res[0][0]['ChassisNo'];
                $data['CustomerDetails'] = DB::select("exec usp_LoadCustomerDetails '" . $data['ChassisNo'] . "'");
                $data['maxMileage'] = DB::select("select ISNULL(max(CONVERT(NUMERIC(18,0),Mileage)) + 1,0) AS max from DealarWarrantyClaim where ChassisNo = '" . $data['ChassisNo'] . "' and ISNUMERIC(Mileage)  = 1");

                $UserId = strtoupper(trim($data['JobCardDetails'][0]['DealarCode']));
                $data['PartsList'] = $this->loadParstList($JobCardNo, 'Parts', $UserId);
                $partsDataFormat = $this->partsDataFormat($data['PartsList']);

                $data ['FinalDataSet'] = [
                    'JobCardNo' => $JobCardNo,
                    'Picture' => [],
                    'ChassisNo' => $data['ChassisNo'],
                    'EngineNo' => $data['CustomerDetails'][0]->engineno,
                    'InvoiceNo' => $data['CustomerDetails'][0]->invoiceno,
                    'CustomerName' => $data['CustomerDetails'][0]->customername,
                    'ProductName' => $data['CustomerDetails'][0]->productname,
                    'Color' => $data['CustomerDetails'][0]->color,
                    'Status' => $data['CustomerDetails'][0]->serviceleft,
                    'Days' => $data['CustomerDetails'][0]->days,
                    'Mileage' => (int)$data['JobCardDetails'][0]['Mileage'],
                    'ProblemName' => $data['JobCardDetails'][0]['ProblemDetails'],
                    'TechnicianName' => $data['JobCardDetails'][0]['TechnicianName'],
                    'fieldsData' => $partsDataFormat,
                ];

                return response()->json([
                    'status' => 'success',
                    'data' => $data ['FinalDataSet'],
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'success',
                'message' => $exception->getMessage() . $exception->getLine(),
            ]);
        }


    }

    public function filterParts(Request $request)
    {

        $userId = Auth::user()->UserId;
        $business = 'P';
        $productCode = $request['query'];
        $result = DB::select("EXEC usp_LoadProduct '$business','$productCode','0','$userId'");
        return response()->json([
            'status' => 'success',
            'parts' => $result
        ]);
    }

    public function checkParts(Request $request)
    {
        $userId = Auth::user()->UserId;
        $business = 'P';
        $productCode = $request['query'];
        $result = DB::select("EXEC usp_LoadProduct '$business','$productCode','0','$userId'");
        return response()->json([
            'status' => 'success',
            'parts' => $result
        ]);
    }

    public function loadParstList($JobCardNo, $ItemType, $UserId)
    {
        $sql = "SELECT 
                    J.*, ISNULL(P.ProductName, OP.ProductName) ProductName,
                    ISNULL(P.PartNo, OP.SMSCode)  PartNo,
                    W.WorkName
                FROM tblJobCardDetailSparepartWork J
                    LEFT JOIN Product P
                            ON J.ItemCode = P.ProductCode
                    LEFT JOIN OtherProduct OP
                            ON J.ItemCode = CONVERT(VARCHAR(10),OP.ProductCode)
                    LEFT JOIN tblWorkSetup W
                            ON J.ItemCode = W.WorkCode AND W.ServiceCenterCode = '$UserId'
                WHERE JobCardNo = '$JobCardNo'
                        AND ItemType = '$ItemType' ";
        return DB::select($sql);
    }

    public function partsDataFormat($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result [] = [
                'InvoiceType' => '',
                'SpareParts' => $item->ProductName,
                'Quantity' => $item->Quantity,
                'UnitPrice' => $item->UnitPrice,
                'TotalPrice' => $item->TotalPrice,
                'serviceCharge' => $item->ServiceCharge,
                'ItemCode' => $item->ItemCode,
            ];
        }
        return $result;
    }

    public function getAllPendingWarranty(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $userID = Auth::user()->UserId;
        $reportType = 0;

        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo . ' 24:59:59';
        $DateTo = date('Y-m-d', strtotime($DateTo));

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }
        $sql = "exec usp_doLoadWarrantyAppovalPendingNew '$DateFrom','$DateTo', '$userID', '$reportType','$PerPage','$CurrentPage' ";

        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function pendingWarrantyEditAndApproved($warrantyId)
    {

        $conn = DB::connection('sqlsrvread');
        $sql = "EXEC usp_doLoadWarrantyInfoForEdit '$warrantyId' ";
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = [];
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        $data = [];

        $getAllParts = DB::table('DealarWarrantyClaimProduct as DWCP')->select('DWCP.WarrantyInvoiceId as InvoiceType', 'P.ProductName as SpareParts',
            'DWCP.Quantity', 'DWCP.UnitPrice', 'DWCP.serviceCharge', 'DWCP.ProductCode as ItemCode', DB::raw("(DWCP.Quantity * DWCP.UnitPrice) as TotalPrice"))
            ->join('Product as P', 'P.ProductCode', '=', 'DWCP.ProductCode')
            ->where('DCWarrantyID', $warrantyId)->get();

        $data['warranty'] = [
            'WarrantyId' => $warrantyId,
            'JobCardNo' => $res[0][0]['JobCardNo'],
            'ChassisNo' => $res[0][0]['ChassisNo'],
            'EngineNo' => $res[0][0]['EngineNo'],
            'InvoiceNo' => $res[0][0]['InvoiceNo'],
            'SlodDate' => date('Y-m-d', strtotime($res[0][0]['SlodDate'])),
            'DealerName' => $res[0][0]['DealerName'] ? $res[0][0]['DealerName'] : '',
            'CustomerName' => $res[0][0]['CustomerName'],
            'ProductName' => $res[0][0]['ProductName'],
            'Color' => $res[0][0]['Color'],
            'OccuranceDate' => $res[0][0]['OccuranceDate'],
            'SourceOfInformation' => $res[0][0]['WarrantySourceId'],
            'Mileage' => (int)$res[0][0]['Mileage'],
            'ServiceSchedule' => $res[0][0]['FreeServiceSchedule'],
            'TypeOfWarranty' => $res[0][0]['WarrantyTypeId'],
            'Seriousness' => $res[0][0]['WarrantySeriousnessId'],
            'TechnicianName' => $res[0][0]['TechnicianName'],
            'ProblemName' => $res[0][0]['ProblemDetails'],
            'Sex' => $res[0][0]['RiderSex'],
            'Weight' => $res[0][0]['RiderWeight'],
            'RoadCondition' => $res[0][0]['RoadCondition'],
            'Age' => $res[0][0]['RiderAge'],
            'RidingStyle' => $res[0][0]['RidingStyle'],
            'RiderProfession' => $res[0][0]['OccupationId'],
            'ProblemIs' => $res[0][0]['WarrantyProblemIsId'],
            'Remedy' => $res[0][0]['WarrantyRemedyId'],
            'Result' => $res[0][0]['WarrantyProblemResultId'],
            'CustomerComments' => $res[0][0]['CustomerComment'],
            'FailureAnalysis' => $res[0][0]['DiagnosisFailureAnalysis'],
            'RemedyResult' => $res[0][0]['RemedyResult'],
            'CauseOfFailure' => $res[0][0]['SuspectedCauseOfFailure'],
            'AdditionalComments' => $res[0][0]['AdditionalComments'],
            'Picture' => $res[1],
            'fieldsData' => $getAllParts,
        ];

        return response()->json([
            'warranty' => $data['warranty'],
            'warrantyImage' => $res[1]
        ]);
    }

    public function delete($warrantyId)
    {
        $MasterCode = Auth::user()->UserId;
        DB::table('DealarWarrantyClaim')->where('DCWarrantyId', $warrantyId)->update([
            'Status' => 2,
            'ApproveBy' => $MasterCode,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully Deleted'
        ]);
    }

    public function getAllApprovedWarranty(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $userID = Auth::user()->UserId;
        $reportType = 1;

        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo;

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }
        $sql = "exec usp_doLoadWarrantyAppovalPendingNew '$DateFrom','$DateTo','$userID', '$reportType','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getAllCancelWarranty(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $userID = Auth::user()->UserId;
        $reportType = 2;

        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo . ' 24:59:59';
        $DateTo = date('Y-m-d', strtotime($DateTo));

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }
        $sql = "exec usp_doLoadWarrantyAppovalPendingNew '$DateFrom','$DateTo','$userID', '$reportType','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getAllClaimWarrantyReport(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $userID = Auth::user()->UserId;
        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo;
        $CustomerCode = $request->CustomerCode;
        if (empty($CustomerCode)) {
            $CustomerCode = '%';
        }
        $Region = $request->Region;
        $ReportType = $request->ApprovedType;
        $Search = $request->Search;

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }

        $sql = "EXEC Usp_reportWarrantyClaimNewNew '$DateFrom','$DateTo','$CustomerCode', '%','$PerPage','$CurrentPage','$Search','$ReportType','$Region','$userID'";

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
        $to = 100;
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

    public function printWarranty($DCWarrantyId)
    {
        //PART ONE SQL
        $sql = "exec usp_doLoadWarrentyDetails '$DCWarrantyId'";
        $firstPart = $this->DBConnectionQuery($sql);
        //PART two SQL
        $chassisNo = $firstPart[0][0]['ChassisNo'];
        $sql2 = "exec usp_doLoadServiceHistories '$chassisNo'";
        $secondPart = $this->DBConnectionQuery($sql2);
        //image
        $warrantyDetailsSql = DB::select("
                    SELECT 
                        DCWCDetailID, 
                        DCWarrantyID, 
                        ProImageThumb, 
                        ProImagePath 
                    FROM DealarWarrantyClaimDetails WHERE DCWarrantyID = '$DCWarrantyId'");

        return response()->json([
            'partsData' => $firstPart[0],
            'detailsData' => $firstPart[0][0],
            'imagesData' => $warrantyDetailsSql,
            //'imagesData' => $firstPart[1],
            'histories' => $secondPart,
        ]);
    }

    public function getSDMSWarrantyClaimList(Request $request)
    {
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $userID = Auth::user()->UserId;
        $DateFrom = $request->DateFrom;
        $DateTo = $request->DateTo;
        $CustomerCode = $request->CustomerCode;
        if (empty($CustomerCode)) {
            $CustomerCode = '%';
        }

        if ($Export == 'Y') {
            $CurrentPage = '%';
        }

        $sql = "EXEC usp_doLoadWarrentyClaimList '$DateFrom','$DateTo','$CustomerCode', '$PerPage','$CurrentPage'";

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
        $to = 100;
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

    public function getCustomer()
    {
        $user = Auth::user();
        if ($user->grpSup == 1) {
            $customerListId = '%';
        } else {
            $customerListId = Auth::user()->UserId;
        }

        $sql = "SELECT * FROM Customer WHERE CustomerCode LIKE '$customerListId' AND CustomerType IN ('E','D','R') AND LEFT(CustomerCode,2) = 'HC' ";
        $customers = DB::select($sql);
        return response()->json([
            'customers' => $customers
        ]);
    }

    public function DBConnectionQuery($sql)
    {
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

    public function warrantyClaimSummaryReport(Request $request)
    {
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $sql = "SELECT 
            CASE 
                WHEN Status = 0 THEN 'Pending' 
                WHEN Status = 1 THEN 'Approved' 
                WHEN Status = 2 THEN 'Cancel' END Approved_Status,
            sum(isnull(DP.UnitPrice,0) + isnull(DP.ServiceCharge,0)) as Total_Cost,
            count(distinct dc.DCWarrantyID) Total
            from DealarWarrantyClaim DC
            left join DealarWarrantyClaimProduct DP on DP.DCWarrantyID=DC.DCWarrantyID 
            where DC.WCDate between '$dateFrom' and '$dateTo'
            group by DC.Status order by DC.Status asc";
        $summary = DB::select($sql);
        return response()->json([
            'summary' => $summary
        ]);
    }

    public function changePartsReceivingStatus(Request $request)
    {
        $DCWarrantyId = $request->DCWarrantyId;
        $ProductCode = $request->ProductCode;
        $time = date("Y-m-d H:i:s");
        DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyID', $DCWarrantyId)->where('ProductCode', $ProductCode)->update([
            'PartsReceivingStatus' => 1,
            'PartsReceivingTime' => $time,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'updated Successfully'
        ]);
    }

    public function changeWarrantyJudgementStatus(Request $request)
    {
        $DCWarrantyId = $request->DCWarrantyId;
        $ProductCode = $request->ProductCode;
        $time = date("Y-m-d H:i:s");
        DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyID', $DCWarrantyId)->where('ProductCode', $ProductCode)->update([
            'WarrantyJudgementByService' => 1,
            'WarrantyJudgementTime' => $time,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully'
        ]);
    }

    public function changeWarrantyJudgementRejectStatus(Request $request)
    {
        $DCWarrantyId = $request->DCWarrantyId;
        $ProductCode = $request->ProductCode;
        $time = date("Y-m-d H:i:s");
        DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyID', $DCWarrantyId)->where('ProductCode', $ProductCode)->update([
            'WarrantyJudgementByService' => 2,
            'WarrantyJudgementTime' => $time,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully'
        ]);
    }

    public function changeFactoryQAStatus(Request $request)
    {
        $DCWarrantyId = $request->DCWarrantyId;
        $ProductCode = $request->ProductCode;
        $time = date("Y-m-d H:i:s");
        DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyID', $DCWarrantyId)->where('ProductCode', $ProductCode)->update([
            'FactoryQA' => 2,
            'FactoryQATime' => $time,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully'
        ]);
    }

    public function changeFactoryQARejectStatus(Request $request)
    {
        $DCWarrantyId = $request->DCWarrantyId;
        $ProductCode = $request->ProductCode;
        $time = date("Y-m-d H:i:s");
        DB::table('DealarWarrantyClaimProduct')->where('DCWarrantyID', $DCWarrantyId)->where('ProductCode', $ProductCode)->update([
            'FactoryQA' => 2,
            'FactoryQATime' => $time,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Updated Successfully'
        ]);
    }

}
