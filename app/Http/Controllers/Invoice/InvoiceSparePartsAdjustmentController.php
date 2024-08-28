<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DealerStock;
use App\Models\JobCard\TblJobCard;
use App\Models\SpareParts\DealerStockAdjustmentDetails;
use App\Models\SpareParts\DealerStockAdjustmentMaster;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceSparePartsAdjustmentController extends Controller
{
    use CommonTrait, CodeGeneration;

    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $adjMaster = DealerStockAdjustmentMaster::select(
            'DealerStockAdjustmentMaster.AdjustmentInvoiceNo',
            DB::raw('convert(date,DealerStockAdjustmentMaster.AdjustmentDate) as AdjustmentDate'),
            'DealerStockAdjustmentMaster.MasterCode',
            'Customer.CustomerName',
            'DealerStockAdjustmentMaster.ReturnStatus',
            'DealerStockAdjustmentMaster.ReturnBy',
        )->join('Customer', 'Customer.CustomerCode', 'DealerStockAdjustmentMaster.MasterCode')
        ->where('DealerStockAdjustmentMaster.ReturnStatus','N');

        if($roleId !=='admin'){
            $adjMaster->where('DealerStockAdjustmentMaster.MasterCode', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $adjMaster->get(),
            ]);
        } else {
            return $adjMaster->paginate($take);
        }
    }

    public function getDemoExcelFile()
    {
        $excel_url = url('/') . '/assets/file/adjustment_file_upload_format.xls';
        return $excel_url;
    }

    public function getAdjustmentSupportingData(Request $request)
    {
        $customerCode = $request->CustomerCode;
        $sql = "SELECT top 10 CustomerCode,CustomerName,concat(CustomerCode,'-',CustomerName) as CustomerName FROM Customer WHERE CustomerCode LIKE '$customerCode%'
         AND CustomerType IN ('E','D','R') AND LEFT(CustomerCode,2) = 'HC'";
        $allDealers = $this->getPdoResult($sql);
        $allDealers = $allDealers[0];

        return response()->json([
            'allDealers' => $allDealers,
        ]);
    }

    public function adjustmentStore(Request $request)
    {

        $adjustmentImportData = $request->ExcelData;
        try {

            DB::beginTransaction();
            //Adjustment master
            $dealerAdjustmentMaster = new  DealerStockAdjustmentMaster();
            $dealerAdjustmentMaster->AdjustmentDate = Carbon::now()->format('Y-m-d');
            $dealerAdjustmentMaster->MasterCode = $request->masterCode['CustomerCode'];
            $dealerAdjustmentMaster->EntryDate = Carbon::now();
            $dealerAdjustmentMaster->EntryBy = Auth::user()->UserId;
            $dealerAdjustmentMaster->save();

            foreach ($adjustmentImportData as $singleData) {
                if ($singleData['Product_Code'] && $singleData['Part_No'] && $singleData['Product_Name'] && $singleData['Unit_Price']) {
                    //Adjustment details
                    $adjDetails = new DealerStockAdjustmentDetails();
                    $adjDetails->AdjustmentInvoiceNo = $dealerAdjustmentMaster->AdjustmentInvoiceNo;
                    $adjDetails->ProductCode = $singleData['Product_Code'];
                    $adjDetails->PartNo = $singleData['Part_No'];
                    $adjDetails->ProductName = $singleData['Product_Name'];
                    $adjDetails->UnitPrice = $singleData['Unit_Price'];
                    $adjDetails->CurrentStock = $singleData['Current_Stock'];
                    $adjDetails->RealCount = $singleData['Real_Count'];
                    $adjDetails->AdjustmentStock = $singleData['Adjustment_Stock'];
                    $adjDetails->save();
                    
                    //Dealer Stock table
                    $existingDealerStock = DealerStock::where('MasterCode', $request->masterCode['CustomerCode'])
                        ->where('ProductCode', $singleData['Product_Code'])->first();

                    if ($existingDealerStock) {
                        DealerStock::where('MasterCode', $request->masterCode['CustomerCode'])
                            ->where('ProductCode', $singleData['Product_Code'])->update([
                                'AdjustmentQuantity' => intval($existingDealerStock->AdjustmentQuantity) + intval($singleData['Adjustment_Stock']),
                                'CurrentStock' => intval($existingDealerStock->CurrentStock) - intval($singleData['Adjustment_Stock'])
                            ]);
                    }
                    else{
                        $newDealerStock = new DealerStock();
                        $newDealerStock->MasterCode= $request->masterCode['CustomerCode'];
                        $newDealerStock->ProductCode= $singleData['Product_Code'];
                        $newDealerStock->ReceiveQuantity= 0;
                        $newDealerStock->SalesQuantity= 0;
                        $newDealerStock->ReturnQuantity= 0;
                        $newDealerStock->AdjustmentQuantity= intval($singleData['Adjustment_Stock']);
                        $newDealerStock->CurrentStock= intval($singleData['Adjustment_Stock'])<0 ? -1*intval($singleData['Adjustment_Stock']):0;
                        $newDealerStock->save();

                    }

                }

            }
            Db::commit();


            return response()->json([
                'status' => 'Success',
                'message' => 'Adjustment Added Successfully'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }

    public function getExistingAdjustment($adjustmentInvoiceNo)
    {

        $getExistingData = DealerStockAdjustmentMaster::select(
            'DealerStockAdjustmentMaster.AdjustmentInvoiceNo',
            'DealerStockAdjustmentMaster.AdjustmentDate',
            'DealerStockAdjustmentMaster.MasterCode',
            'DealerStockAdjustmentMaster.ReturnStatus',
            'DealerStockAdjustmentMaster.ReturnBy',
            'DealerStockAdjustmentMaster.EntryDate',
            'DealerStockAdjustmentMaster.EntryBy',
            'DealerStockAdjustmentDetails.ProductCode as Product_Code',
            'DealerStockAdjustmentDetails.PartNo as Part_No',
            'DealerStockAdjustmentDetails.ProductName as Product_Name',
            'DealerStockAdjustmentDetails.UnitPrice as Unit_Price',
            'DealerStockAdjustmentDetails.CurrentStock as Current_Stock',
            'DealerStockAdjustmentDetails.RealCount as Real_Count',
            'DealerStockAdjustmentDetails.AdjustmentStock as Adjustment_Stock',
            'DealerStockAdjustmentDetails.AdjustmentStock as Adjustment_Stock',
            'Customer.CustomerName',
        )
            ->join('Customer', 'Customer.CustomerCode', 'DealerStockAdjustmentMaster.MasterCode')
            ->join('DealerStockAdjustmentDetails', 'DealerStockAdjustmentDetails.AdjustmentInvoiceNo', 'DealerStockAdjustmentMaster.AdjustmentInvoiceNo')
            ->where('DealerStockAdjustmentMaster.AdjustmentInvoiceNo', $adjustmentInvoiceNo)
            ->get();
        return response()->json([
            'data' => $getExistingData,
        ]);
    }

    public function returnAdjustmentInvoice(Request $request)
    {


        try {
            //Master Update
            DealerStockAdjustmentMaster::where('AdjustmentInvoiceNo', $request->adjustmentInvoiceNo)
                ->update([
                    'ReturnStatus' => 'Y',
                    'ReturnDate' => Carbon::now()->format('Y-m-d'),
                    'ReturnBy' => Auth::user()->UserId,
                    'ReturnIpAddress' => $_SERVER['SERVER_ADDR'],
                ]);

            $detailsData = DealerStockAdjustmentDetails::where('AdjustmentInvoiceNo', $request->adjustmentInvoiceNo)->get();
            foreach ($detailsData as $singleData) {
                //Dealer Stock table
                $existingDealerStock = DealerStock::where('MasterCode', $request->masterCode['CustomerCode'])
                    ->where('ProductCode', $singleData['ProductCode'])->first();
                if ($existingDealerStock){
                    DealerStock::where('MasterCode', $request->masterCode['CustomerCode'])
                        ->where('ProductCode', $singleData['ProductCode'])->update([
                            'AdjustmentQuantity' => intval($existingDealerStock->AdjustmentQuantity) - intval($singleData['AdjustmentStock']),
                            'CurrentStock' => intval($existingDealerStock->CurrentStock) + intval($singleData['AdjustmentStock'])
                        ]);
                }

            }
            return response()->json([
                'status' => 'Success',
                'message' => 'Adjustment Return Successfully'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }


    }

}
