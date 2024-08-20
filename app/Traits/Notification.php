<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

trait Notification
{
    function sendSms($receipient, $text)
    {
        try {
            $ip = '192.168.100.213';
            $userId = 'finance';
            $password = 'Asdf1234';
            $text = urlencode($text);
            $smsUrl = "http://{$ip}/httpapi/sendsms?userId={$userId}&password={$password}&smsText=" . $text . "&commaSeperatedReceiverNumbers=" . $receipient;
            $smsUrl = preg_replace("/ /", "%20", $smsUrl);
            $response = file_get_contents($smsUrl);
            return $response;
        } catch (\Exception $exception) {

        }
    }

    public function sendMail($email,$instance)
    {
        try {
            Mail::to($email)->send($instance);
        } catch (\Exception $exception) {

        }
    }
}