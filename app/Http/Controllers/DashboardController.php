<?php

namespace App\Http\Controllers;

use App\Models\DealerReceiveInvoiceDetails;
use App\Models\Invoice;
use App\Models\InvoiceReceiveSurvey;
use App\Models\InvoiceReceiveSurveyAnswers;
use App\Models\OrderInvoiceMaster;
use App\Traits\CommonTrait;
use App\Traits\DashboardTrait;
use Carbon\Carbon;
use Dompdf\FrameDecorator\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DashboardTrait,CommonTrait;
    public function receivables(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        return $this->doLoadMyReceivable($userId,$search)->paginate($take);
    }

    public function getReceivableById($invoiceNo)
    {
        return $this->doLoadMyReceivableById($invoiceNo)->get();
    }

    public function storeSurvey(Request $request)
    {
        $request->validate([
            'invoiceNo' => 'required'
        ]);
        try {
            $invoiceNo = $request->invoiceNo;
            $surveyAnswerIDs = $request->SurveyAnswerIDs;
            $comment = $request->SurveyComment;
            $userId = Auth::user()->UserId;
            foreach ($surveyAnswerIDs as $key => $survey){
                $id = (int)$survey;
                $survey = new InvoiceReceiveSurvey();
                $survey->InvoiceNo = $invoiceNo;
                $survey->SurveyQuestionID = $key;
                $survey->SurveyAnswerID   = $id;
                $survey->Comment = $comment;
                $survey->EntryBy = $userId;
                $survey->EntryDate = Carbon::now();
                $survey->EntryIPAddress = \request()->ip();
                $survey->save();
            }

            $invoiceUpdate = Invoice::where('InvoiceNo','=',$invoiceNo)->first();
            $invoiceUpdate->IsReceiveSurvey = 'Y';
            $invoiceUpdate->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Survey Submitted Successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'.$exception->getMessage()
            ],500);
        }
    }

    public function storeReceivable(Request $request)
    {
        $request->validate([
            'invoiceNo' => 'required'
        ]);
        try {
            $invoiceNo = $request->invoiceNo;
            $userId = Auth::user()->UserId;
            $data = $this->doStoreReceivable($userId,$invoiceNo);
            if (isset($data[0]->rcount) && intval($data[0]->rcount) > 0) {
                $receiveId = $data[0]->ReceiveID;
                $receiveDetails = DealerReceiveInvoiceDetails::where('ReceiveID',$receiveId)->get();
                if (!empty($receiveDetails)) {
                    foreach ($receiveDetails as $detail) {
                        $this->doUpdateStock($userId,$detail->ProductCode,$detail->ReceivedQnty);
                    }
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Receive Successful',
                'data' => $data
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $exception->getMessage()
            ],500);
        }
    }

    public function invoiceReceiveSurveyData()
    {
        $questions = DB::table('InvoiceReceiveSurveyQuestions')->select('SurveyQuestionID','SurveyQuestion')->get();
        $questions->map(function ($question) {
            $question->SurveyQuestion = mb_convert_encoding($question->SurveyQuestion,'UTF-8', 'UTF-8');
        });
        return response()->json([
            'questions' => $questions,
            'answers' => DB::table('InvoiceReceiveSurveyAnswers')->select('SurveyAnswerID','SurveyAnswer','SurveyQuestionID')->get()
        ]);
    }
    public function getCurrenBalanceData(){

        $spareParts = $this->customerCurrentBalance('P');
        $bike = $this->customerCurrentBalance('C');
//        $userId = Auth::user()->UserId;
//        $sql = "exec usp_doLoadCustomerDueFroDMS '$userId'";
//
//        $conn = DB::connection('sqlsrvread');
//        $pdo = $conn->getPdo()->prepare($sql);
//        $pdo->execute();
//        $res = array();
//        do {
//            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
//            $res[] = $rows;
//        } while ($pdo->nextRowset());

        return response()->json([
            'spareParts' => $spareParts,
            'bike' => $bike,
        ]);
    }

    public function getAllMyOrders(Request $request){
        $masterCode = Auth::user()->UserId;
        $sql ="SELECT top 10 oim.orderno as OrderNo, oim.orderdate as OrderDate, oim.ordertime as OrderTime , Convert(INT,sum((UnitPrice+VAT)*Quantity)) as Total
                FROM OrderInvoiceMaster oim 
                INNER JOIN OrderInvoiceDetails oid ON oim.OrderNo = oid.OrderNo 
                INNER JOIN Customer C ON C.CustomerCode = oim.MasterCode
                INNER JOIN CustomerType CT ON CT.CustomerType = C.CustomerType
                WHERE oim.MasterCode = '$masterCode'
                GROUP BY oim.OrderNo, oim.orderdate ,oim.ordertime 
                ORDER BY oim.OrderTime DESC";
        $list =DB::select(DB::raw($sql));
        return response()->json([
           'data' => $list
        ]);


    }
    public function getOrderDetails($orderNo){
        $Order =intval($orderNo);
        $sql=$this->doLoadMyOrders($Order);
        return response()->json([
           'data' => $sql
        ]);
    }
    public function pendingOrders(Request $request){
        $UserId = Auth::user()->UserId;
        $RoleId = Auth::user()->RoleId;
        $sql = $this->doLoadPendingOrders($RoleId,$UserId);
        return response()->json([
            'data' => $sql
        ]);
    }
    public function storeApproved(Request $request)
    {
        $request->validate([
            'orderNo' => 'required'
        ]);
        try {
            $orderNo = $request->orderNo;
            $userId = Auth::user()->UserId;
            $roleId = Auth::user()->RoleId;
            $approval = OrderInvoiceMaster::where('OrderNo',$orderNo)->first();
            if ($roleId=='tm' ||$roleId=='se' ){
                $approval->Level1Approved='Y';
                $approval->Level1ApprovedBy=$userId;
                $approval->Level1ApprovedDate=Carbon::now();

            }elseif($roleId=='hos' ||$roleId=='hose'){
                $approval->Level2Approved='Y';
                $approval->Level2ApprovedBy=$userId;
                $approval->Level2ApprovedDate=Carbon::now();
            }else{
                $approval->Level3Approved='Y';
                $approval->Level3ApprovedBy=$userId;
                $approval->Level3ApprovedDate=Carbon::now();
            }
                $approval->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Approved Successful'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $exception->getMessage()
            ],500);
        }
    }
    public function editApproved(Request $request)
    {
        $request->validate([
            'orderNo' => 'required'
        ]);
        try {
            $orderNo = $request->orderNo;
            $list = DB::table('OrderInvoiceMaster as m')
                ->select('m.OrderNo','d.ProductCode','p.ProductName','d.Quantity','d.UnitPrice','d.VAT')
                ->join('OrderInvoiceDetails as d','d.OrderNo','=','m.OrderNo')
                ->join('Product as p','p.ProductCode','=','d.ProductCode')
                ->where('m.OrderNo',$orderNo)
                ->get();

            return response()->json([
                'data' => $list
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $exception->getMessage()
            ],500);
        }
    }
}
