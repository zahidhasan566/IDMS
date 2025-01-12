<?php

namespace App\Http\Controllers;

use App\Models\PreBookingRe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
