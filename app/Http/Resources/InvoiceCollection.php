<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data'=>$this->collection->transform(function ($invoice){
                return [
                    'InvoiceID'=> $invoice->InvoiceID,
                    'UserCode'=> $invoice->MasterCode,
                    'InvoiceNo'=> $invoice->InvoiceNo,
                    'InvoiceDate'=> date('Y-m-d',strtotime($invoice->InvoiceDate)),
                    'InvoiceTime'=> date("h:i:sa", strtotime($invoice->InvoiceTime)),
                    'CustomerName'=> $invoice->CustomerName,
                    'MobileNo'=> $invoice->MobileNo,
                    'NID'=> $invoice->NID,
                    'ProductCode'=> $invoice->DealarInvoiceDetails->ProductCode,
                    'ProductName'=> $invoice->DealarInvoiceDetails->ProductName,
                    'Quantity'=> $invoice->DealarInvoiceDetails->Quantity,
                    'UnitPrice'=> $invoice->DealarInvoiceDetails->UnitPrice,
                    'Discount'=> $invoice->DealarInvoiceDetails->Discount,
                    'ChassisNo'=> $invoice->DealarInvoiceDetails->ChassisNo,
                    'EngineNo'=> $invoice->DealarInvoiceDetails->EngineNo,
                    'SalesType'=> $invoice->DealarInvoiceDetails->SalesType,
                ];
            })
        ];
    }
}
