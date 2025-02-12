<?php

namespace App\Http\Controllers;

use App\Jobs\EstatementJob;
use App\Mail\EstatementMail;
use App\Models\InvoiceReceiveSurveyAnswers;
use App\Models\InvoiceReceiveSurveyQuestion;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\SpPaginationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use App\Traits\CommonTrait;

class ReportController extends Controller
{
    //REPORT
    use CommonTrait;
    public function getReportSupportingData(){
        return response()->json([
            'customer' => $this->loadCustomer()
        ]);
    }
    public function getInvoiceReceiveSurevyReportSupportingData(){
        $invoiceReceiveSurveyQuestion = InvoiceReceiveSurveyQuestion::select('SurveyQuestionID','SurveyQuestion')->get();
        return response()->json([
            'customer' => $this->loadCustomer(),
            'invoiceReceiveSurveyQuestion' => $invoiceReceiveSurveyQuestion
        ]);
    }

    public function getUserWiseRegionData(){
        return response()->json([
            'region' => $this->loadRegion()
        ]);
    }

    public function getSalesSummeryReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;

        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportSalesSummary '$dateFrom', '$dateTo', '$CustomerCode','','C','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getBikeSalesReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $BookingCode = $request->BookingCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportProductInvoice '$dateFrom', '$dateTo', '$CustomerCode','$BookingCode','','C','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    public function getPreBookAllocationReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportPreBookAllocation '$dateFrom', '$dateTo', '$CustomerCode','','C','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    public function getFlagshipBikeSalesReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportFlagshipBikeSales '$dateFrom', '$dateTo', '','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getSparePartsSalesReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = "exec usp_reportSparePartsInvoice '$dateFrom', '$dateTo', '$CustomerCode','','P','$userID','$PerPage','$CurrentPage'";
        //return $sql;
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getBikeOrderReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportProductOrder '$dateFrom', '$dateTo', '$CustomerCode','','C','$userID','$PerPage','$CurrentPage' ";
        //return $sql;
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getSparePartsAffiliatorSalesReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage    = 20;
        $Export     = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){ $CurrentPage = '%'; }

        $sql = " exec usp_reportSparePartsInvoice '$dateFrom', '$dateTo', '$CustomerCode','','C','$userID','$PerPage','$CurrentPage','Y' ";
        //echo $sql; exit();
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    //getSparePartsAffiliatorSalesReport

    public function getSparePartsReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportProductOrder '$dateFrom', '$dateTo', '$CustomerCode','','P','$userID','$PerPage','$CurrentPage' ";
        //return $sql;
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    public function getSparePartsSalesReportExcel(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = "exec usp_reportSparePartsInvoice '$dateFrom', '$dateTo', '$CustomerCode','','P','$userID','$PerPage','$CurrentPage'";

        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();

        $res = array();

        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        //return $allData;

        $filename = 'spare_parts_sales';
        header("Content-Disposition: attachment; filename=\"{$filename}.xls\"");
        header("Content-Type: application/vnd.ms-excel;");
        header("Pragma: no-cache");
        header("Expires: 0");
        $out = fopen("php://output", 'w');


        foreach ($res[0] as $data) {
            fputcsv($out, $data, "\t");
        }
        fclose($out);
    }

    public function getBikeStockReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $roleId = Auth::user()->RoleId;
        if($roleId == 'customer'){
            $CustomerCode = Auth::user()->UserId;
        }
        $sql = " exec usp_reportProductStock  '$CustomerCode','','C','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getSparePartsStockReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportSparePartsStockNew  '$CustomerCode','','P','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getEstimatedFreeService(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportEstimatedFreeServiceSchedule  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getEstimatedPaidService(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportEstimatedPaidServiceSchedule  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getServiceRatio(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $Region = $request->RegionName;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportRegionWiseDealarServiceRatio  '$dateFrom', '$dateTo', '$CustomerCode','$Region','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getCustomerCSISummary(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportCustomerCSISummary  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getOpeningCloseingStock (Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $dateTo = $request->DateTo;
        $ProductType = $request->ProductType;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        if($ProductType == "Bike"){
            $sql = " exec usp_reportCustomerOpeningClosingStockBike  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        }else{
            $sql = " exec usp_reportCustomerOpeningClosingStockSpareParts  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        }
        
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getCustomerBikeSalesFeedBack(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_reportCustomerBikeSalesFeedBack  '$dateFrom', '$dateTo', '$CustomerCode','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getClaimWarranty(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $ChassisNo = $request->ChassisNo;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo. ' 24:59:59';
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));
        //return $dateTo;
        $regionName = $request->RegionName;
        $approveType = $request->ApproveType;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "exec usp_reportWarrantyClaimDetails  '$dateFrom', '$dateTo', '$CustomerCode','$ChassisNo','$approveType','$regionName','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getInvoiceSurveyReportData(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;

        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = "exec usp_invoicereceivesurveyreport '$dateFrom','$dateTo','$CustomerCode',$PerPage,'$CurrentPage'";

        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getDealerInvoiceSurveyReportData(Request $request){

        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;

        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = "exec usp_loadSurveyFeedbackReport '$dateFrom','$dateTo'";

        $conn = DB::connection('sqlsrvread');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();

        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());

        return $res[0];
    }
    public function getServiceSummaryReport(Request $request){

        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_doLoadServiceSummaryReportUpdate '$dateFrom', '$dateTo', '$CustomerCode','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function getScrapProductsReport(Request $request){
        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = "exec usp_reportScrapProducts '$dateFrom', '$dateTo', '$CustomerCode','','P','$userID','$PerPage','$CurrentPage'";

        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    
    

    
}
