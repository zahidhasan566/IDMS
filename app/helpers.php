<?php

use Jenssegers\Agent\Agent;

if (! function_exists('storeRenamedFile')) {
    function storeRenamedFile($file, $prefix) {
        $extension = $file->getClientOriginalExtension();
        $newName = $prefix . time() . '_' . rand(1, 10) . '.' . $extension;
        // Store the file in the 'assets/lostdocuments' directory
        $file->move(public_path('assets/lostdocuments'), $newName);
        return $newName;
    }
}

if (! function_exists('generateDownloadFileLink')) {
    function generateDownloadFileLink($dataList)
    {
        //$localBaseUrl = env('APP_URL') . '/assets/lostdocuments/';
        $localBaseUrl ='http://dms.yamahabd.com/';
//        $s3BaseUrl = env('AWS_S3_DOCUMENT_URL');
        $s3BaseUrl = "https://dms-signature.s3.ap-southeast-1.amazonaws.com/public/uploads/";

        foreach ($dataList as &$data) {
            foreach (['Delivery Chalan', 'G.D Copy', 'Other Document', 'GDCopy', 'DeliveryChalan', 'OtherDocument'] as $key) {
                if (isset($data[$key]) && !empty($data[$key])) {
                    $fileName = $data[$key];
                    $localFilePath = public_path('assets/lostdocuments/' . $fileName);
                    if (file_exists($localFilePath)) {
                        $data[$key] = $localBaseUrl . $fileName;
                    } else {
                        $data[$key] = $s3BaseUrl . $fileName;
                    }
                }
            }
        }
        return $dataList;
    }
}

if (! function_exists('getDeviceState')) {
    function getDeviceState()
    {
        $agent = new Agent();

        if ($agent->isBrowser()) {
            $deviceState = $agent->browser() . ' ' . $agent->version($agent->browser());
        } elseif ($agent->isRobot()) {
            $deviceState = $agent->robot();
        } elseif ($agent->isMobile()) {
            $deviceState = $agent->device();
        } else {
            $deviceState = 'Unidentified User Agent';
        }

        return $deviceState;
    }
}

if (! function_exists('numberToWord')) {

    function numberToWord($number)
    {
        $my_number = $number;

        if (($number < 0) || ($number > 999999999))
        {
            throw new Exception("Number is out of range");
        }
        $Kt = floor($number / 10000000); /* Koti */
        $number -= $Kt * 10000000;
        $Gn = floor($number / 100000);  /* lakh  */
        $number -= $Gn * 100000;
        $kn = floor($number / 1000);     /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100);      /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10);       /* Tens (deca) */
        $n = $number % 10;               /* Ones */

        $res = "";

        if ($Kt)
        {
            $res .= numberToWord($Kt) . " Koti ";
        }
        if ($Gn)
        {
            $res .= numberToWord($Gn) . " Lakh";
        }

        if ($kn)
        {
            $res .= (empty($res) ? "" : " ") .
                numberToWord($kn) . " Thousand";
        }

        if ($Hn)
        {
            $res .= (empty($res) ? "" : " ") .
                numberToWord($Hn) . " Hundred";
        }

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
            "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
            "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
            "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
            "Seventy", "Eigthy", "Ninety");

        if ($Dn || $n)
        {
            if (!empty($res))
            {
                $res .= " and ";
            }

            if ($Dn < 2)
            {
                $res .= $ones[$Dn * 10 + $n];
            }
            else
            {
                $res .= $tens[$Dn];

                if ($n)
                {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res))
        {
            $res = "zero";
        }

        return $res;
    }
}