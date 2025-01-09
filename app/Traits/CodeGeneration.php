<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait CodeGeneration
{
    public function generateDealerUserCode($userId)
    {
        $maxCode = DB::select(DB::raw("select MAX(RIGHT(UserId,5)) as MaxNo from UserManager where LEFT(UserId,7) = '$userId-'"));
        if ($maxCode) {
            $maxCode = $maxCode[0]->MaxNo;
            $lastCode = $maxCode + 1;
            return $userId.'-'.sprintf("%05d", $lastCode);
        } else {
            return $userId.'-00001';
        }
    }

    public function generateAdvanceMoneyReceipt($customerCode)
    {
        $combinedCode = 'RE'.$customerCode.Carbon::now()->format('y');
        $combinedLength = strlen($combinedCode);
        $maxCode = DB::select(DB::raw("select MAX(MoneyRecNo) as MaxNo FROM AdvanceMoneyReceipt WHERE LEFT(MoneyRecNo,'$combinedLength') = '$combinedCode'"));
        $maxCode = $maxCode[0]->MaxNo;
        if ($maxCode === null) {
            $nextCode = $combinedCode.'000001';
        } else {
            $nextCode = substr($maxCode,$combinedLength);
            $nextCodeInc = $nextCode + 1;
            $nextCode = sprintf("%0".strlen($nextCode)."d", $nextCodeInc);
            $nextCode = $combinedCode.$nextCode;
        }
        return $nextCode;
    }
    public function generateJobCardInvoiceNo()
    {
        $userId  = Auth::user()->UserId;
        $combinedCode = $userId.Carbon::now()->format('y');
        $combinedLength = strlen($combinedCode);
        $maxCode = DB::select(DB::raw("select MAX(InvoiceNo) as MaxNo FROM DealarInvoiceMaster WHERE LEFT(InvoiceNo,'$combinedLength') = '$combinedCode'"));
        $maxCode = $maxCode[0]->MaxNo;
        if ($maxCode === null) {
            $nextCode = $combinedCode.'000001';
        } else {
            $nextCode = substr($maxCode,$combinedLength);
            $nextCodeInc = $nextCode + 1;
            $nextCode = sprintf("%0".strlen($nextCode)."d", $nextCodeInc);
            $nextCode = $combinedCode.$nextCode;
        }
        return $nextCode;
    }

    public function generateInvoiceNo($CustomerCode, $invoiceDate){

        $maxInvoice = DB::select("SELECT MAX(InvoiceNo) as InvoiceNo
                    FROM DealarInvoiceMaster
                    WHERE MasterCode = '$CustomerCode'
                      AND YEAR(InvoiceDate) = YEAR('$invoiceDate')
                      AND CHARINDEX('SYS', InvoiceNo) = 0");
        $maxInvoice = $maxInvoice[0]->InvoiceNo;

        $InvYear = DB::select("SELECT RIGHT(CAST(YEAR('$invoiceDate') AS VARCHAR(4)), 2) as InvYear");
        $InvYear = $InvYear[0]->InvYear;

        $invoiceSerial = DB::select("SELECT CASE WHEN '$maxInvoice' IS NULL OR '$maxInvoice' = '' THEN 1
                            ELSE CAST(
                                SUBSTRING(
                                  '$maxInvoice', 
                                  LEN('$CustomerCode'+'/'+'$InvYear')+1, 
                                LEN('$maxInvoice') - LEN('$CustomerCode'+'/'+ '$InvYear')) AS NUMERIC(18, 0)) + 1
                            END as invoiceSerial");
        $invoiceSerial = $invoiceSerial[0]->invoiceSerial;

        $invoiceNo = DB::select("SELECT '$CustomerCode'+'/'+ '$InvYear' +
                                  REPLICATE('0', 5 - LEN(CAST('$invoiceSerial' AS VARCHAR(35))))+
                                  CAST('$invoiceSerial' AS VARCHAR(35)) as invoiceNo");
        return $invoiceNo[0]->invoiceNo;
    }

    public function generateVerifyCode(){
        $code = DB::select("SELECT CAST(CAST(ROUND((9 * RAND()), 0) AS INT) AS VARCHAR(1))+
                  CAST(CAST(ROUND((9 * RAND()), 0) AS INT) AS VARCHAR(1))+
                  CAST(CAST(ROUND((9 * RAND()), 0) AS INT) AS VARCHAR(1))+
                  CAST(CAST(ROUND((9 * RAND()), 0) AS INT) AS VARCHAR(1))+
                  CAST(CAST(ROUND((9 * RAND()), 0) AS INT) AS VARCHAR(1)) as VerifyCode");
        return $code[0]->VerifyCode;
    }

    public function generatetechnicianNo()
    {

        $combinedCode = 'SE-';
        $combinedLength = strlen($combinedCode);
        $maxCode = DB::select(DB::raw("select MAX(TechnicianCode) as MaxNo FROM tblTechnicianSetup WHERE LEFT(TechnicianCode,'$combinedLength') = '$combinedCode'"));
        $maxCode = $maxCode[0]->MaxNo;
        if ($maxCode === null) {
            $nextCode = $combinedCode . '00001';
        }
        else {
            $nextCode = substr($maxCode,$combinedLength);
            $nextCodeInc = $nextCode + 1;
            $nextCode = sprintf("%0".strlen($nextCode)."d", $nextCodeInc);
            $nextCode = $combinedCode.$nextCode;
        }
        return $nextCode;
    }
    public function getMoneyReceiptNo($depot, $business) {
        $sql = "exec usp_generateMoneyReceiptNo '$depot', '$business'";
        return DB::select($sql);
    }

    public function generateTestRideId()
    {
        $combinedCode = 'TR'.date('y');
        $combinedLength = strlen($combinedCode);
        $sql = DB::select(DB::raw("select MAX(RideId) as MaxNo FROM TestRiders WHERE LEFT(RideId,'$combinedLength') = '$combinedCode'")) ;
        $maxCode = $sql[0]->MaxNo;
        if ($maxCode === null) {
            $nextCode = $combinedCode.'000001';
        } else {
            $nextCode = substr($maxCode,$combinedLength);
            $nextCodeInc = $nextCode + 1;
            $nextCode = sprintf("%0".strlen($nextCode)."d", $nextCodeInc);
            $nextCode = $combinedCode.$nextCode;
        }
        return $nextCode;
    }

}