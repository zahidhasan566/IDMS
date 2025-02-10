<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class InvoiceResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'invoiceno' => $this->invoiceno,
            'InvoiceDate' => date('Y-m-d',strtotime($this->invoicedate)),
            'invoicetime' => $this->invoicetime,

        ];
    }
}
