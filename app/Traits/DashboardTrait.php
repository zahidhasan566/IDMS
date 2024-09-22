<?php

namespace App\Traits;

use App\Models\DealerStock;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

trait DashboardTrait
{
    public function doLoadMyReceivable($userId, $search)
    {
        return DB::table('Invoice', 'i')
            ->join('InvoiceDetails as invd', 'i.InvoiceNo', 'invd.Invoiceno')
            ->join('Product as p', 'invd.ProductCode', 'p.ProductCode')
            ->join('InvoiceDetailsBatch as idb', function ($q) {
                $q->on('invd.Invoiceno', 'idb.Invoiceno')
                    ->on('invd.ProductCode', 'idb.ProductCode');
            })
            ->join('StockBatch as sb', function ($q) {
                $q->on('idb.BatchNo', 'sb.BatchNo')
                    ->on('invd.ProductCode', 'sb.ProductCode');
            })
            ->join('CustomerMapping as cm', 'i.CustomerCode', 'cm.CustomerCode')
            ->leftJoin('DealarReceiveInvoiceMaster as DRIM', 'I.InvoiceNo', 'DRIM.InvoiceNo')
            ->where('i.Returned', 'N')
            ->where('cm.CustomerMasterCode', $userId)
            ->where('DRIM.InvoiceNo', NULL)
            ->where('SB.DepotCode', 'H')
            ->where(function ($q) use ($search) {
                if ($search != '') {
                    $q->where('i.InvoiceNo', '=', $search);
                }
            })
            ->groupBy('i.InvoiceNo', 'i.InvoiceDate', 'i.DeliveryDate', 'i.DeliveryDate', 'i.OrderDate', 'i.Discount', 'i.Net', 'i.IsReceiveSurvey')
            ->orderBy('i.InvoiceNo')
            ->select('i.InvoiceNo', 'i.InvoiceDate', 'i.DeliveryDate', 'i.OrderDate', 'i.Discount', 'i.Net as Total', 'i.IsReceiveSurvey');
    }

    public function doLoadMyReceivableById($invoiceNo)
    {
        return DB::table('Invoice', 'i')
            ->join('InvoiceDetails as invd', 'i.InvoiceNo', 'invd.Invoiceno')
            ->join('Product as p', 'invd.ProductCode', 'p.ProductCode')
            ->join('InvoiceDetailsBatch as idb', function ($q) {
                $q->on('invd.Invoiceno', 'idb.Invoiceno')
                    ->on('invd.ProductCode', 'idb.ProductCode');
            })
            ->join('StockBatch as sb', function ($q) {
                $q->on('idb.BatchNo', 'sb.BatchNo')
                    ->on('invd.ProductCode', 'sb.ProductCode');
            })
            ->join('CustomerMapping as cm', 'i.CustomerCode', 'cm.CustomerCode')
            ->leftJoin('DealarReceiveInvoiceMaster as DRIM', 'I.InvoiceNo', 'DRIM.InvoiceNo')
            ->where('i.Returned', 'N')
            ->where('i.InvoiceNo', $invoiceNo)
            ->where('DRIM.InvoiceNo', NULL)
            ->where('SB.DepotCode', 'H')
            ->select('i.InvoiceNo', 'p.ProductName', 'SB.BatchNo as ChassisNo', 'SB.EngineNo', 'invd.SalesQTY as Quantity', DB::raw("invd.SalesTP+invd.SalesVat as UnitPrice"));
    }

    public function doStoreReceivable($userId, $invoiceNo)
    {
        $sql = "exec usp_DealarReceiveInsert_idms '$userId', '$invoiceNo'";
        return DB::select($sql);
    }

    public function doUpdateStock($userId, $productCode, $quantity)
    {
        $dealerStock = DealerStock::where('MasterCode', $userId)->where('ProductCode', $productCode)->first();
        if ($dealerStock) {
            DealerStock::where('MasterCode', $userId)
                ->where('ProductCode', $productCode)
                ->update([
                    'ReceiveQuantity' => $dealerStock->Quantity + $quantity,
                    'CurrentStock' => $dealerStock->CurrentStock + $quantity
                ]);
        } else {
            DealerStock::create([
                'MasterCode' => $userId,
                'ProductCode' => $productCode,
                'ReceiveQuantity' => $quantity,
                'SalesQuantity' => 0,
                'ReturnQuantity' => 0,
                'AdjustmentQuantity' => 0,
                'CurrentStock' => $quantity,
            ]);
        }
    }

    public function doLoadMyOrders($Order){
        $sql = "exec usp_LoadMyOrdersNew '$Order'";
        $list = DB::select($sql);
        return $list;

    }

    public function doLoadPendingOrders($userId,$roleId){
        $sql = "exec SP_OrderMasterPendingList '$roleId','$userId'";
        $list = DB::select($sql);
        return $list;

    }
}