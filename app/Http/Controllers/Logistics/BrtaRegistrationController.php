<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\JobCard\DealarInvoiceDetails;
use App\Models\JobCard\TblJobCard;
use App\Models\Logistics\BrtaRegistrationStatus;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrtaRegistrationController extends Controller
{
    use CommonTrait, CodeGeneration;

    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $userId = Auth::user()->UserId;
        $roleId = Auth::user()->RoleId;

        $brtaRegistration = BrtaRegistrationStatus::select(
            'BRTA_RegistrationStatus.BRTA_RegistrationStatusID',
            'BRTA_RegistrationStatus.ChassisNO',
            'BRTA_RegistrationStatus.IssueDate',
            'BRTA_RegistrationStatus.BRTA_RegistrationNumber',
            DB::raw("case
                    when BRTA_RegistrationStatus.BRTA_BankDeposite = 'Y' then 'Done'
                    when BRTA_RegistrationStatus.BRTA_BankDeposite = 'N' then 'Pending'
                    else ''
                end as BRTA_BankDeposite"),
            DB::raw("case
                    when BRTA_RegistrationStatus.RegDocumentComplete = 'Y' then 'Done'
                    when BRTA_RegistrationStatus.RegDocumentComplete = 'N' then 'Pending'
                    else ''
                end as RegDocumentComplete"),
            DB::raw("case
                    when BRTA_RegistrationStatus.FileReceivedByCustomer = 'Y' then 'Done'
                    when BRTA_RegistrationStatus.FileReceivedByCustomer = 'N' then 'Pending'
                    else ''
                end as FileReceivedByCustomer"),
            DB::raw("case
                    when BRTA_RegistrationStatus.UnregisteredOrDuePayment = 'Y' then 'Done'
                    when BRTA_RegistrationStatus.UnregisteredOrDuePayment = 'N' then 'Pending'
                    else ''
                end as UnregisteredOrDuePayment"),
            'BRTA_RegistrationStatus.EntryBy',
        )
            ->join('Customer', 'Customer.CustomerCode', 'BRTA_RegistrationStatus.EntryBy')
            ->join('DealarInvoiceDetails', 'DealarInvoiceDetails.ChassisNo', 'BRTA_RegistrationStatus.ChassisNo')
            ->join('DealarInvoiceMaster', 'DealarInvoiceMaster.InvoiceID', 'DealarInvoiceDetails.InvoiceID')
            ->where('BRTA_RegistrationStatus.EntryBy', '=', $userId)
            ->where(function ($q) use ($search) {
                $q->where('BRTA_RegistrationStatus.ChassisNO', 'like', '%' . $search . '%');
                $q->Orwhere('BRTA_RegistrationStatus.BRTA_RegistrationStatusID', 'like', '%' . $search . '%');
                $q->Orwhere('BRTA_RegistrationStatus.BRTA_RegistrationNumber', 'like', '%' . $search . '%');
            })
            ->orderBy('BRTA_RegistrationStatus.BRTA_RegistrationStatusID', 'desc');

        if ($roleId !== 'admin') {
            $brtaRegistration->where('BRTA_RegistrationStatus.EntryBy', $userId);
        }
        if ($request->type === 'export') {
            return response()->json([
                'data' => $brtaRegistration->get(),
            ]);
        } else {
            return $brtaRegistration->paginate($take);
        }
    }

    public function getExistingBrtaRegistrationStatus($brtaRegistrationStatusId)
    {
        $brtaRegistration = BrtaRegistrationStatus::where('BRTA_RegistrationStatusID', $brtaRegistrationStatusId)->first();

        return response()->json([
            'brtaRegistration' => $brtaRegistration,
        ]);
    }

    public function updateBrtaRegistrationStatus(Request $request)
    {

        try {
            $brtaRegistration = BrtaRegistrationStatus::where('BRTA_RegistrationStatusID', $request->brtaRegistrationStatusId);

            if ($request->registrationMethod === 'r') {
                $brtaRegistration->update([
                    'RegistrationMethod' => $request->registrationMethod,
                    'IssueDate' => $request->issueDate,
                    'BRTA_BankDeposite' => $request->bankDepositSlip,
                    'BRTA_RegistrationNumber' => $request->brtaRegistrationNumber
                ]);
            } else if ($request->registrationMethod === 'f') {
                $brtaRegistration->update([
                    'RegistrationMethod' => $request->registrationMethod,
                    'IssueDate' => $request->issueDate,
                    'RegDocumentComplete' => $request->regDocFileComplete,
                    'FileReceivedByCustomer' => $request->fileReceiveByCustomer
                ]);
            } else {
                $brtaRegistration->update([
                    'RegistrationMethod' => $request->registrationMethod,
                    'RegDocumentComplete' => $request->regDocFileComplete,
                ]);
            }

            $userId = Auth::user()->UserId;
            //Check Customer Mobile
            $checkCustomerMobile =  DealarInvoiceDetails::select(
                                                    'DealarInvoiceMaster.MobileNo'
                                     )
                                    ->join('DealarInvoiceMaster','DealarInvoiceMaster.InvoiceID','DealarInvoiceDetails.InvoiceID')
                                     ->where('DealarInvoiceDetails.ChassisNo',$request->chassisNo)
                                      ->first();

            //Send SMS
            $customerMobile = $checkCustomerMobile->MobileNo;
            $smsContent = $this->prepareSmsText($request->registrationMethod);
            $this->sendSmsQ($customerMobile, '8809617615000', 'Dms_V2', 'Brta_Registration_status', '', $userId, 'smsq', $smsContent);

            return response()->json([
                'status' => 'Success',
                'message' => 'Brta Registration Status Updated Successfully'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!' . $exception->getMessage()
            ], 500);
        }


    }

    public function prepareSmsText($registrationMethod)
    {
        $customerName = Auth::user()->UserName;
        if ($registrationMethod == 'r') {
            $smsText = "Dear Customer, BRTA registration process of your motorcycle has been completed. Please, collect tax token & acknowledgement slip from our showroom. Please, bring with original sales receipt.";
            $smsText .= "\n Thanks & Regards, " . $customerName . ".";
        } elseif ($registrationMethod == 'f') {
            $smsText = "Dear Customer, your registration paper has been completed.  Please, collect the paper from our showroom. Please, bring with original sales receipt.";
            $smsText .= "\n Thanks & Regards, " . $customerName . ".";
        } elseif ($registrationMethod == 'u') {
            $smsText = "Dear Customer, your registration paper has been completed. Please, collect the paper or apply for the BRTA registration from our showroom. Please, bring with original sales receipt.";
            $smsText .= "\n Thanks & Regards, " . $customerName . ".";
        } else {
            $smsText = "Dear Customer, BRTA bank slip of your motorcycle has been completed. Please, collect the slip from our showroom. Please, bring with original sales receipt.";
            $smsText .= "\n Thanks & Regards, " . $customerName . ".";
        }
        return $smsText;

    }

}
