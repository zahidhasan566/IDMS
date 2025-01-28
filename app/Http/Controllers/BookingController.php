<?php

namespace App\Http\Controllers;

use App\Models\DealerBookingAllocation;
use App\Models\DealerStock;
use App\Models\FlagshipStockAdjustmentMaster;
use App\Models\PreBookingRe;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function getByCode($bookingCode)
    {
        try {
            $booking = PreBookingRe::join('DealerBookingAllocation as DBA','DBA.BookingCode','PreBookingRe.BookingCode')
                ->where('PreBookingRe.BookingCode',$bookingCode)
                ->where('DBA.Active','Y')
                ->where('DBA.CustomerCode',Auth::user()->UserId)
                ->first();
            return response()->json([
                'data' => $booking
            ]);
        } catch(\Exception $exception) {
            return response()->json([
                'data' => null
            ]);
        }
    }
    public function getDemoExcelFile()
    {
        $excel_url = url('/') . '/assets/file/prebook_allocation_file_upload_format.xls';
        return $excel_url;
    }

    public function storePreBookAllocationData(Request $request){
        $importData = $request->ExcelData;
        DB::beginTransaction();
        try {
            foreach ($importData as $singleData) {
                if ($singleData['Booking_Code'] && $singleData['Dealer_Code']) {
                    $dealerBookingAllocation =  new DealerBookingAllocation();
                    $dealerBookingAllocation->BookingCode = intval($singleData['Booking_Code']);
                    $dealerBookingAllocation->CustomerCode = $singleData['Dealer_Code'];
                    $dealerBookingAllocation->Active = 'Y';
                    $dealerBookingAllocation->DelerveryStatus = 'N';
                    $dealerBookingAllocation->save();
                }

            }
            Db::commit();


            return response()->json([
                'status' => 'Success',
                'message' => 'Allocation Added Successfully'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }
    }
}
