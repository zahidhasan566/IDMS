<?php

namespace App\Http\Controllers\Sap;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PreBookingRe;
use App\Models\Sap\SapUserLog;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrebookingController extends Controller
{
    use commonTrait;
    public function getPreBookSupportingData(){
        return response()->json([
//            'customer' => $this->loadCustomer(),
            'customer' => PreBookingRe::selectRaw('distinct DeliveryDealerCode as CustomerCode, DeliveryLocationName as CustomerName')->get()->toArray(),
            'products' => PreBookingRe::selectRaw('distinct ProductCode, ProductName')->get()->toArray(),
            'bookingMode' => PreBookingRe::selectRaw('distinct BookingMode')->get()->toArray(),
        ]);
    }
    public function getPreBookingReport(Request $request){

        $CurrentPage = $request->pagination['current_page'];
        $PerPage = 20;
        $Export = $request->Export;
        $CustomerCode = $request->CustomerCode;
        $ProductCode = $request->ProductCode;
        $bookingMode = $request->bookingMode;
        $dateFrom = $request->DateFrom;
        $dateTo = $request->DateTo;
        $userID = Auth::user()->UserId;
        if ($Export == 'Y'){
            $CurrentPage = '%';
        }
        $sql = " exec usp_doLoadPreBookingReport  '$dateFrom', '$dateTo', '$CustomerCode','$ProductCode','$bookingMode','','$userID','$PerPage','$CurrentPage' ";
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }
    public function storePreBookingCustomer(Request $request)
    {
        $dt = date('Y-m-d H-i-s A');
        try {
            $singleCustomer = $request->all();

            if (!empty($singleCustomer['booking_id'])
                && !empty($singleCustomer['booking_code'])
                && !empty($singleCustomer['status'])
                && !empty($singleCustomer['booking_amount'])
                && !empty($singleCustomer['booking_mode'])
                && !empty($singleCustomer['expected_delivery_date'])
                && !empty($singleCustomer['payment_method'])
                && !empty($singleCustomer['payment_status'])
                && !empty($singleCustomer['payment_transaction_id'])
                && !empty($singleCustomer['customer']['customer_name'])
                && !empty($singleCustomer['customer']['customer_mobile'])
                && !empty($singleCustomer['product']['product_id'])
                && !empty($singleCustomer['product']['product_name'])
//                && !empty($singleCustomer['product']['product_price'])
                && !empty($singleCustomer['color']['color_id'])
                && !empty($singleCustomer['color']['color_name'])
                && !empty($singleCustomer['delivery_location']['delivery_location_id'])
                && !empty($singleCustomer['delivery_location']['delivery_location_name'])
                && !empty($singleCustomer['delivery_location']['dealer_code'])
            ) {
                DB::beginTransaction();
                //Customer Prebooking Create
                $customer = new PreBookingRe();
                $customer->BookingId = $singleCustomer['booking_id'];
                $customer->BookingCode = $singleCustomer['booking_code'];
                $customer->Status = $singleCustomer['status'];
                $customer->BookingAmount = $singleCustomer['booking_amount'];
                $customer->BookingMode = $singleCustomer['booking_mode'];
                $customer->ExpectedDeliveryDate = $singleCustomer['expected_delivery_date'];
                $customer->PaymentMethod = $singleCustomer['payment_method'];
                $customer->PaymentStatus = $singleCustomer['payment_status'];
                $customer->PaymentTransactionId = $singleCustomer['payment_transaction_id'];
                $customer->BookingCreatedAt = $singleCustomer['booking_created_at'];
                $customer->BookingUpdatedAt = $singleCustomer['booking_updated_at'];

                $customer->CustomerCode = $singleCustomer['customer']['customer_id'];
                $customer->CustomerName = $singleCustomer['customer']['customer_name'];
                $customer->CustomerMobile = $singleCustomer['customer']['customer_mobile'];
                $customer->CustomerEmail = $singleCustomer['customer']['customer_email'];
                $customer->CustomerAddress = $singleCustomer['customer']['customer_address'];
                $customer->CustomerDistrictCode = $singleCustomer['customer']['district_id'];
                $customer->CustomerDistrict = $singleCustomer['customer']['district'];
                $customer->CustomerThanaCode = $singleCustomer['customer']['thana_id'];
                $customer->CustomerThana = $singleCustomer['customer']['thana'];
                $customer->ProductCode = $singleCustomer['product']['product_id'];

                $customer->ProductName = $singleCustomer['product']['product_name'];
                $customer->ProductPrice = $singleCustomer['product']['product_price'];
                $customer->ProductColorCode = $singleCustomer['color']['color_id'];
                $customer->ProductColorName =  $singleCustomer['color']['color_name'];
                $customer->DeliveryLocationCode = $singleCustomer['delivery_location']['delivery_location_id'];
                $customer->DeliveryLocationName =  $singleCustomer['delivery_location']['delivery_location_name'];
                $customer->DeliveryDealerCode = $singleCustomer['delivery_location']['dealer_code'];
                $customer->DeliveryDistrictCode = $singleCustomer['delivery_location']['delivery_district_id'];
                $customer->DeliveryDealerCode = $singleCustomer['delivery_location']['dealer_code'];
                $customer->PrebookUrl = $singleCustomer['invoice_url'];
                $customer->IUser = Auth::user()->UserId;
                $customer->IDate = Carbon::now();
                $customer->IpAddress = $request->ip();
                $customer->save();
                DB::commit();

                file_put_contents('public/log/prebooking/prebooking_customer_success-' . $dt . '.txt', json_encode($singleCustomer) . "\n", FILE_APPEND);
                return response()->json([
                    'status' => 'Success',
                    'message' => 'PreBooking Added Successfully',
                    'BookingCode' => $singleCustomer['booking_code']
                ], 200);


            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Missing Required Parameter',
                    'CustomerCount' => $singleCustomer
                ], 422);
            }
        }
        catch (\Exception $exception) {
                DB::rollBack();
                file_put_contents('public/log/prebooking/prebooking_customer_error-' . $dt . '.txt', $exception->getMessage() . '-' . $exception->getLine() . "\n", FILE_APPEND);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong!' . $exception->getMessage() . '-' . $exception->getLine()
                ], 500);
            }
    }
}
