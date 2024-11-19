<?php

namespace App\Http\Controllers\inquiry;

use App\Http\Controllers\Controller;
use App\Services\SpPaginationService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryFollowUpReportController extends Controller
{
    use CommonTrait;
    public function supportingData()
    {
        return response()->json([
            'customer' => $this->loadCustomer(),
            'products' => $this->getBusinessWiseProduct('p','','')
        ]);
    }
    public function report(Request $request)
    {
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page,$take);
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $type = $request->type;
        $productCode= $request->product?$request->product['id']:'';
        if (!empty($startDate) && !empty($endDate)) {
            $roleId = Auth::user()->RoleId;
            $userId = Auth::user()->UserId;
            if ($roleId !== 'customer') {
                if (!empty($request->customer)) {
                    $customer = $request->customer;
                } else {
                    $customer = '';
                }
            } else {
                $customer = $userId;
            }
            if ($type === 'export') {
                $sp = "exec usp_doLoadInquiryConversionSummary '$startDate','$endDate','$customer','$productCode',$take,1";
                return response()->json([
                    'data' => SpPaginationService::getPdoResult($sp)
                ]);
            } else {
                $sp = "exec usp_doLoadInquiryConversionSummary '$startDate','$endDate','$customer','$productCode',$take,1";
            }

            return SpPaginationService::paginate2($sp,$take,$offset);
        }
        return response()->json([
            'status' => 'error',
            'data' => [[]]
        ]);
    }
}
