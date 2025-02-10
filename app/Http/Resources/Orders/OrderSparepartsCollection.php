<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderSparepartsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data'=>$this->collection->transform(function ($order){
                return[
                    'ProductCode'=>$order->ProductCode,
                    'ProductName'=>$order->ProductName,
                    'PartNo'=>$order->PartNo,
                    'UnitPrice'=>intval($order->UnitPrice),
                    'Vat'=>intval($order->VAT) ,
                    'Quantity'=>0,
                    'CurrentStock'=>isset($order->CurrentStock) ? $order->dealerstock->CurrentStock: 0,
                ];
            }),
        ];
    }
}
