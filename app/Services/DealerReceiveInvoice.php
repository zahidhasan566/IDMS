<?php

namespace App\Services;

use App\Models\DealarReceiveInvoiceDetails;
use App\Models\DealarReceiveInvoiceMaster;
use App\Models\DealerStock;
use App\Models\Invoice;
use App\Traits\CommonTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DealerReceiveInvoice
{
    use CommonTrait;
    public function doStorePartialReceivable($userId, $invoiceNo, $orderDetails)
    {
        DB::beginTransaction();
        try {
            if (count($orderDetails)) {
                $invoice = Invoice::where('InvoiceNo', $invoiceNo)->first();
                $dealerReceiveInvoiceMaster = DealarReceiveInvoiceMaster::where('InvoiceNo', $invoiceNo)->first();
                if (!$dealerReceiveInvoiceMaster) {
                    $receiveInvoiceMaster = DealarReceiveInvoiceMaster::create([
                        'InvoiceNo' => $invoice->InvoiceNo,
                        'InvoicePeriod' => $invoice->InvoicePeriod,
                        'InvoiceDate' => $invoice->InvoiceDate,
                        'Business' => $invoice->Business,
                        'MasterCode' => $userId,
                        'CustomerCode' => $invoice->CustomerCode,
                        'ReceiveDate' => Carbon::now()->format('Y-m-d H:i:s'),
                        'DepotCode' => 'H',
                        'DeliveryDate' => $invoice->DeliveryDate,
                        'DeliveryTime' => $invoice->DeliveryTime
                    ]);
                } else {
                    $receiveInvoiceMaster = $dealerReceiveInvoiceMaster;
                }

                if ($receiveInvoiceMaster) {
                    $invoiceDetails = DB::table('InvoiceDetails','invd')
                        ->join('Product as p','p.ProductCode','invd.ProductCode')
                        ->join('InvoiceDetailsBatch as idb',function($q) {
                            $q->on('idb.Invoiceno','invd.Invoiceno')
                                ->on('invd.ProductCode','idb.ProductCode');
                        })
                        ->leftjoin('StockBatch as sb',function ($q) use ($invoice){
                            $q->on('sb.BatchNo','idb.BatchNo')
                                ->on('idb.ProductCode','sb.ProductCode')
                                ->where('sb.DepotCode',$invoice->DepotCode);
                        })

                        ->where('invd.Invoiceno',$invoiceNo)
                        ->where('idb.quantity','>',0)
                        ->select('p.ProductCode','idb.Quantity','invd.SalesTP','invd.SalesVat','idb.BatchNo','idb.EngineNo','p.Color','p.FuelUsed',
                            'p.HorsePower','p.RPM','p.CubicCapacity','p.WheelBase','p.Weight','p.TireSizeFront','p.TireSizeRear','P.Manufacturer','P.Origin',
                            'idb.Quantity','sb.DepotCode')
                        ->get();


                    if (count($invoiceDetails) > 0) {
                        foreach ($invoiceDetails as $row) {
                            $receiving = array_filter($orderDetails,function ($item) use ($row) {
                                return $item['ChassisNo'] === $row->BatchNo;
                            });
                            if (count($receiving)) {
                                $receiving = array_values($receiving);
                                $receiving = $receiving[0];
                                if (intval($receiving['ReceiveQty']) > 0) {
                                    if ($this->insertIntoDealerReceiveInvoiceDetails($receiveInvoiceMaster,$row,$receiving['ReceiveQty'])) {
                                        if ($invoice->Business === 'P') {
                                            if (!$this->doUpdateStock($userId,$row->ProductCode,$receiving['ReceiveQty'])) {
                                                DB::rollBack();
                                                Log::error('Stock update failed. See the detail log.');
                                                return false;
                                            }
                                        }
                                    } else {
                                        DB::rollBack();
                                        return false;
                                    }
                                }
                                if (intval($receiving['DamagedQty']) > 0) {
                                    if (!$this->insertIntoDamagedReceiveInvoiceDetails($invoiceNo,$row,$receiving['DamagedQty'],$receiving['DamagedImage'])) {
                                        DB::rollBack();
                                        return false;
                                    }
                                }
                            }
                        }
                    } else {
                        DB::rollBack();
                        Log::error('No details found! : ' . $userId . ' InvoiceNo: ' . $invoiceNo);
                        return false;
                    }
                    DB::commit();
                    return $receiveInvoiceMaster;
                } else {
                    DB::rollBack();
                    Log::error('Creation Failed! UserId: ' . $userId . ' InvoiceNo: ' . $invoiceNo);
                    return false;
                }
            }
            Log::error('No details passed!');
            return false;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return false;
        }
    }

    private function insertIntoDealerReceiveInvoiceDetails($receiveInvoiceMaster, $row, $quantity)
    {
        try {
            $importYear = DB::table('Receive','r')->join('ReceiveDetails as rd',function($q) {
                $q->on('r.ReceiveNo','rd.ReceiveNo');
            })->where('rd.BatchNo',$row->BatchNo)->select(DB::raw("YEAR(ReceiveDate) as ImportYear"))->first();


            return DealarReceiveInvoiceDetails::create([
                'ReceiveID' => $receiveInvoiceMaster->ReceiveID,
                'ProductCode' => $row->ProductCode,
                'ReceivedQnty' => $quantity,
                'UnitPrice' => $row->SalesTP,
                'Vat' => $row->SalesVat,
                'ChassisNo' => $row->BatchNo,
                'EngineNo' => $row->EngineNo,
                'Color' => $row->Color ?$row->Color : '',
                'FuelUsed' => $row->FuelUsed,
                'HorsePower' => $row->HorsePower,
                'RPM' => $row->RPM,
                'CubicCapacity' => $row->CubicCapacity,
                'WheelBase' => $row->WheelBase,
                'Weight' => $row->Weight,
                'TireSizeFront' => $row->TireSizeFront,
                'TireSizeRear' => $row->TireSizeRear,
                'Seats' => 'Two',
                'NoofTyre' => 'Two',
                'NoofAxel' => 'Two',
                'ClassOfVehicle' => 'Motorcycle',
                'MakerName' => $row->Manufacturer,
                'MakerCountry' => $row->Origin,
                'EngineType' => '4 Stroke',
                'NumberofCylinders' => 'One',
                'ImportYear' => $importYear ? $importYear->ImportYear: ''
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    private function insertIntoDamagedReceiveInvoiceDetails($invoiceNo,$row, $quantity,$image)
    {
        try {
            $fileName = '';
            if (!empty($image)) {
                $fileName = $this->imageUpload($image,'damaged_',public_path('uploads/damaged/'));
            }
            DB::table('DamagedReceiveDetails')->insert([
                'InvoiceNo' => $invoiceNo,
                'ProductCode' => $row->ProductCode,
                'DamagedQty' => $quantity,
                'UnitPrice' => $row->SalesTP,
                'Vat' => $row->SalesVat,
                'ChassisNo' => $row->BatchNo,
                'EngineNo' => $row->EngineNo,
                'DamagedImage' => $fileName
            ]);
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    private function doUpdateStock($userId, $productCode, $quantity)
    {
        try {
            $dealerStock = DealerStock::where('MasterCode', $userId)->where('ProductCode', $productCode)->first();
            if ($dealerStock) {
                DealerStock::where('MasterCode', $userId)
                    ->where('ProductCode', $productCode)
                    ->update([
                        'ReceiveQuantity' => $dealerStock->ReceiveQuantity + $quantity,
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
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}