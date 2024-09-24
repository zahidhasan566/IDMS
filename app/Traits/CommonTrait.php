<?php

namespace App\Traits;

use App\Models\Banks;
use App\Models\Challan;
use App\Models\Customer;
use App\Models\CustomerMapping;
use App\Models\JobCard\TblWorkSetup;
use App\Models\Menu;
use App\Models\Product;
use App\Models\Role;
use App\Models\TestRide\TestRideAgents;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\RoleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

trait CommonTrait
{
    public function userModalData() {
        return response()->json([
            'status' => 'success',
            'roles' => RoleService::list(),
            'business' => BusinessService::list(),
            'department' => DepartmentService::list(),
            'allSubMenus' => Menu::whereNotIn('MenuID',['Dashboard','Users'])->with('allSubMenus')->orderBy('MenuOrder','asc')->get()
        ]);
    }
    public function roleList(){
        $list = Role::Select('RoleId','RoleName')->get();
        return  $list;
    }
    public function searchProduct(Request $request){
        $ProductCode = $request->ProductCode;
        $products= Product::select('ProductCode','ProductName')->where('Business','C')->where('Active','Y')->where('MRP','>','0')->where('UnitPrice','>','10')
            ->where(function ($q) use ($ProductCode){
                $q->where('ProductName', 'LIKE', $ProductCode . '%');
                $q->orWhere('ProductCode', 'LIKE', $ProductCode . '%');
            })->orderBy('ProductCode','desc')->get();
        return  $products;

    }

    public function getBikeByProductCode(Request $request){
        $ProductCode = $request->ProductCode;
        $product = Product::select('ProductCode','ProductName','Vat','UnitPrice')
            ->where('ProductCode',$ProductCode)->first();
        return  $product;
    }

    public function searchParts($productCode,$business ){
        $userId  = Auth::user()->UserId;
        $products= Product::select(
            'Product.ProductCode',
            'Product.ProductName',
            'Product.UnitPrice',
            'Product.PartNo',
            'Product.Vat',
            'Product.MRP',
            'd.CurrentStock',
            DB::raw("CONCAT(Product.ProductName,'-',Product.ProductCode,'-',Product.PartNo) AS FullProduct")
        )
           ->leftJoin('DealarStock as d',function ($join)use($userId){
               $join->on('d.ProductCode','Product.ProductCode');
               $join->where('d.MasterCode',$userId);
            })
            ->where('Product.Business',$business)
            ->where('Product.Active','Y')
            ->where('Product.MRP','>','0')
            ->where('Product.UnitPrice','>','0')
            ->where('Product.SMSOrder','Y')
            ->where(function ($q) use ($productCode){
                $q->where('Product.ProductName', 'LIKE', $productCode . '%');
                $q->orWhere('Product.ProductCode', 'LIKE', $productCode . '%');
                $q->orWhere('Product.PartNo', 'LIKE', '%'.$productCode . '%');
            })->orderBy('Product.ProductCode','ASC')->get();


        return  $products;

    }

    public function searchService($searchServiceName ){

        $services = TblWorkSetup::select(
            'TblWorkSetup.ServiceCenterCode',
            'TblWorkSetup.WorkCode',
            'TblWorkSetup.WorkName',
            'TblWorkSetup.WorkRate',
            DB::raw("CONCAT(TblWorkSetup.WorkCode,'-',TblWorkSetup.WorkName) AS FullProduct")
        )
            ->where('TblWorkSetup.ServiceCenterCode', Auth::user()->UserId)
            ->where('TblWorkSetup.Active','Y')
            ->where(function ($q) use ($searchServiceName){
                $q->where('TblWorkSetup.WorkName', 'LIKE','%'. $searchServiceName . '%');
                $q->orWhere('TblWorkSetup.WorkCode', 'LIKE','%'. $searchServiceName . '%');
            })->orderBy('TblWorkSetup.WorkCode','ASC')->get(100);

        return  $services;
    }

    public function getPartsByProductCode(Request $request){

        $ProductCode = $request->ProductCode;
        $product = Product::select('Product.ProductCode','Product.ProductName','Product.Vat','Product.UnitPrice','d.CurrentStock')
            ->Join('DealarStock as d', 'd.ProductCode','Product.ProductCode')
            ->where('Product.ProductCode',$ProductCode)->first();
        return  $product;
    }

    public function getPdoResult($sql)
    {
        $conn = DB::connection('sqlsrv');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());
        return $res;
    }

    public function customerInfo(){
        $business = 'C';
        $customers = DB::table('CustomerMapping')->select('CustomerMapping.*','c.CustomerName','c.DepotCode','c.PaymentMode','b.BusinessName')
            ->join('Customer as c ','c.CustomerCode','CustomerMapping.CustomerCode')
            ->join('Business as b','b.Business','CustomerMapping.Business')
            ->where('CustomerMapping.CustomerMasterCode', Auth::user()->UserId)
            ->where('c.Business',$business)->orderBy('CustomerMapping.CustomerCode','ASC')->get();

        return $customers;

    }

    public function salesType($customerCode){

        $customers = Customer::where('CustomerCode',$customerCode)->get();
        return $customers;

    }

    public function allCustomer($customerId){

//        $sql ="SELECT * FROM Customer WHERE CustomerCode LIKE '$customerId' AND CustomerType IN ('E','D','R') AND LEFT(CustomerCode,2)='HC'";
        $customerList = Customer::where('CustomerCode','like',$customerId)
            ->whereIn('CustomerType',['E','D','R'])
            ->where('CustomerCode','like','%'.'HC'.'%')->get();
//        dd($customerList);
        return $customerList;
    }

    public function doLoadMyStock($userId,$productCode){
            $sql = DB::select("exec usp_dealerCurrentStock '$userId', '$productCode'");
            return $sql;
    }

    public function getBusinessWiseProduct($business, $procode, $withotherproduct = 0){
        $mastercode =Auth::user()->UserId;
        $product = DB::select("exec usp_LoadProduct  '$business','$procode','$withotherproduct','$mastercode'");
        return $product;
//        return response()->json([
//            'data' => $product
//        ]);
    }

    public function getAllListForRide(){
        $YRCRegion= TestRideAgents::select('YRCRegion')->distinct()->get();
        $PresentBike= TestRideAgents::select('PresentBike')->distinct()->get();
        $Customer= Customer::select('CustomerCode','CustomerName',DB::raw("CONCAT(CustomerCode,': ', CustomerName )as CustomerInfo"))->distinct()->get();
        return response()->json([
            'YRCRegions'=>$YRCRegion,
            'PresentBikes'=>$PresentBike,
            'Customers'=>$Customer
        ]);
    }

    public function getAllTestRideAgents(){
        $agents= TestRideAgents::select('Name','AgentId','DealerId')->distinct()->get();
        return response()->json([
            'agents'=>$agents,
        ]);
    }

    public function encode($param) {

    }

    public function decode($param) {

    }

    public function sendSms($receipient, $smstext='Sample Text') {
        $ip = '192.168.100.213';
        $userId = 'motors';
        $password = 'Asdf1234';
        $smstext = urlencode($smstext);
        $smsUrl = "http://{$ip}/httpapi/sendsms?userId={$userId}&password={$password}&smsText=" . $smstext . "&commaSeperatedReceiverNumbers=" . $receipient;
        $smsUrl = preg_replace("/ /", "%20", $smsUrl);
        $response = file_get_contents($smsUrl);
        return json_decode($response);
    }

    public function loadCustomer(){
        $userid = Auth::user()->UserId;
        return DB::select("exec usp_reportCustomerList '$userid'");
    }

    public function loadRegion(){
        $userid = Auth::user()->UserId;
        return DB::select("exec usp_doLoadUserWiseRegionList '$userid'");
    }

    public function loadInvoiceDetails($invoiceno){
        $sql = " SET NOCOUNT ON ; exec doLoadInvoiceDetails  '$invoiceno' ";
        return DB::select($sql);
    }
    public function stockProductList(){
        $userId = Auth::user()->UserId;
        $allParts= Product::select('Product.*','DealarStock.CurrentStock')->leftjoin('DealarStock',function ($q) use ($userId){
            $q->on('DealarStock.ProductCode', 'Product.ProductCode');
            $q->where('DealarStock.MasterCode', $userId);
        })
            ->where('Product.Business','P')
            ->where('Product.Active','Y')
            ->where('Product.MRP','>','0')
            ->where('Product.UnitPrice','>','0')
            ->where('Product.SMSOrder','Y')->get()->toArray();
        return $allParts;
    }

    function getReportData($sql, $PerPage, $CurrentPage, $Export){
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
        $last_page  = $pages_count;
        $from = 1;
        $to = 100;
        if ($Export != 'Y'){
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
            'data' => generateDownloadFileLink($res[0]),
            'paginationData' => $paginationData
        ]);
    }

    function change_array_keys($arrays, $key_changes) {
        foreach ($arrays as $array) {
            foreach ($key_changes as $old_key => $new_key) {
                if (array_key_exists($old_key, $array)) {
                    dd($array);
                    $array[$new_key] = $array[$old_key]; // Assign the value of the old key to the new key
                    unset($array[$old_key]);             // Remove the old key
                }
            }
        }
        return $arrays;
    }

    public function userWiseAcess(){

        $userID = Auth::user()->UserId;
        $userid = strtoupper(trim($userID));

        $partsReceivingUsers = config('app.parts_receiving_users');

        $partsUserExists = in_array($userid,$partsReceivingUsers);
        if($partsUserExists){
            $dataoutput['partsUserExists'] = true;
        }else{
            $dataoutput['partsUserExists'] = false;
        }

        $warrantyJudgementUsers = config('app.warranty_judgement_users');
        $warrantyJudgeUserExists = in_array($userid,$warrantyJudgementUsers);
        if($warrantyJudgeUserExists){
            $dataoutput['warrantyJudgeUserExists'] = true;
        }else{
            $dataoutput['warrantyJudgeUserExists'] = false;
        }

        $factoryQAUsers = config('app.factory_qa_users');
        $factoryQAUserExists = in_array($userid,$factoryQAUsers);
        if($factoryQAUserExists){
            $dataoutput['factoryQAUserExists'] = true;
        }else{
            $dataoutput['factoryQAUserExists'] = false;
        }

        return $dataoutput;
    }

    function sendSmsQ($to, $sId, $applicationName, $moduleName, $otherInfo, $userId, $vendor, $message)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.102.10/apps/api/send-sms/sms-master',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'To='.$to.'&SID='.$sId.'&ApplicationName='.urlencode($applicationName).'&ModuleName='.urlencode($moduleName).'&OtherInfo='.urlencode($otherInfo).'&userID='.$userId.'&Message='.$message.'&SmsVendor='.$vendor,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    public function doLoadSdmsCustomer($customercode)
    {
        $sql = "SELECT C.*,	CM.CustomerCode CustomerCodeSP, CC.PaymentMode PaymentModeSP
                    FROM Customer C 
                        LEFT JOIN CustomerMapping CM
                            ON C.CustomerCode = CM.CustomerMasterCode
                            AND CM.Business = 'P'
                        LEFT JOIN Customer CC
                            ON CC.CustomerCode = CM.CustomerCode
                    WHERE C.CustomerCode = '$customercode'";

        return DB::select($sql);
    }

    //CUSTOMER 1 MONTH OF CURRENT BALANCE
    public function customerCurrentBalance($business)
    {
        $startDate =  Carbon::now()->subDay(30)->format('Y-m-d');
        $endDate =  Carbon::now()->format('Y-m-d');
        $customerCode =Auth::user()->UserId;
        //return $customerCode;
        $customer = $this->doLoadSdmsCustomer($customerCode);
        if (!empty($customer)) {
            $paymentMode = $business === 'P' ? $customer[0]->PaymentModeSP : $customer[0]->PaymentMode; //!empty($customer[0]->PaymentMode) ? $customer[0]->PaymentMode : '';
            $depotCode = !empty($customer[0]->DepotCode) ? $customer[0]->DepotCode : '';
            $customerCode = $business === 'P' ? $customer[0]->CustomerCodeSP : $customerCode;
            $sql = "exec sp_CustomerLedgerNew '$startDate','$endDate','$customerCode','$paymentMode','$depotCode'";
            $data = $this->getPdoResult($sql);
            $dataSet['customer_ledger_opening'] = $data[0];
            $dataSet['customer_ledger_details'] = $data[1];
            $dataSet['customer_ledger_closing'] = $data[2];
            $dataSet['masterInfo'] = [
                'customerName' => !empty($customer[0]->CustomerName) ? $customer[0]->CustomerName : '',
                'contactPerson' => !empty($customer[0]->ContactPerson) ? $customer[0]->ContactPerson : '',
                'customerCode' => $customerCode,
                'paymentMode' => $business === 'P' ? $customer[0]->PaymentModeSP : $customer[0]->PaymentMode,
                'address1' => !empty($customer[0]->Add1) ? $customer[0]->Add1 : '',
                'address2' => !empty($customer[0]->Add2) ? $customer[0]->Add2 : '',
                'mobile' => !empty($customer[0]->Phone) ? $customer[0]->Phone : '',
                'business' => $business,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'opening' => !empty($dataSet['customer_ledger_opening'][0]['Opening']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Opening']) . ')' : $dataSet['customer_ledger_opening'][0]['Opening'],
                'openingRaw' => $dataSet['customer_ledger_opening'][0]['Opening'],
                'dueAmount' => ($dataSet['customer_ledger_opening'][0]['DueAmount'] < 0) ? '(' . abs($dataSet['customer_ledger_opening'][0]['DueAmount']) . ')' : $dataSet['customer_ledger_opening'][0]['DueAmount'],
                'dueDays' => !empty($dataSet['customer_ledger_opening'][0]['DueDays']) ? $dataSet['customer_ledger_opening'][0]['DueDays'] : 0,
                'invoice' => !empty($dataSet['customer_ledger_opening'][0]['Invoice']) ? $dataSet['customer_ledger_opening'][0]['Invoice'] : '',
                'payment' => !empty($dataSet['customer_ledger_opening'][0]['Payment']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Payment']) . ')' : 0,
                'adjustment' => !empty($dataSet['customer_ledger_opening'][0]['Adjustment']) ? '(' . abs($dataSet['customer_ledger_opening'][0]['Adjustment']) . ')' : 0,
                'invoiceFooter' => $dataSet['customer_ledger_closing'][0]['Invoice'],
                'paymentFooter' => $dataSet['customer_ledger_closing'][0]['Payment'],
                'adjustmentFooter' => $dataSet['customer_ledger_closing'][0]['Adjustment'],
                'invoiceGrand' => $dataSet['customer_ledger_closing'][0]['InvoiceTotal'],
                'paymentGrand' => $dataSet['customer_ledger_closing'][0]['PaymentTotal'],
                'adjustmentGrand' => $dataSet['customer_ledger_closing'][0]['AdjustmentTotal']
            ];
            return $dataSet;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No Customer Found!'
            ], 500);
        }
    }
}