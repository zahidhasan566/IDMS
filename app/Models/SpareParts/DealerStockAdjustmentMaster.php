<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerStockAdjustmentMaster extends Model
{
    use HasFactory;

    protected $table = "DealerStockAdjustmentMaster";
    public $primaryKey = 'AdjustmentInvoiceNo';
    protected $guarded = [];
    public $timestamps = false;
}
