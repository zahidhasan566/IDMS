<?php

namespace App\Services;

use App\Models\DealerStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public $userId;
    public $productCode;
    public $quantity = '';

    public function __construct($userId, $productCode,$quantity = '')
    {
        $this->userId = $userId;
        $this->productCode = $productCode;
        $this->quantity = $quantity;
    }

    public function check()
    {
        return DealerStock::where('MasterCode',$this->userId)->where('ProductCode',$this->productCode)->pluck('CurrentStock')->first();
    }

    public function dealerProductStockOut()
    {
        try {
            $stock = DealerStock::where('MasterCode',$this->userId)->where('ProductCode',$this->productCode)->select('SalesQuantity','CurrentStock')->first();
            if ($stock) {
                DealerStock::where('MasterCode',$this->userId)->where('ProductCode',$this->productCode)->update([
                    'SalesQuantity' => intval($stock->SalesQuantity) + $this->quantity,
                    'CurrentStock' => intval($stock->CurrentStock) - intval($this->quantity)
                ]);
            }
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function dealerProductStockIn()
    {
        try {
            if ($this->quantity > 0) {
                $stock = DealerStock::where('MasterCode', $this->userId)->where('ProductCode', $this->productCode)
                    ->select('SalesQuantity', 'ReturnQuantity', 'CurrentStock')->first();
                if ($stock) {
                    DealerStock::where('MasterCode', $this->userId)->where('ProductCode', $this->productCode)->update([
                        'SalesQuantity' => intval($stock->SalesQuantity) - $this->quantity,
                        'ReturnQuantity' => intval($stock->ReturnQuantity) + $this->quantity,
                        'CurrentStock' => intval($stock->CurrentStock) + intval($this->quantity)
                    ]);
                }
            }
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}