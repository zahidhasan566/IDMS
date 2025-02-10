<?php

namespace App\Models\SpareParts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerStockAdjustmentDetails extends Model
{
    use HasFactory;

    protected $table = "DealerStockAdjustmentDetails";
    protected $guarded = [];
    public $timestamps = false;
}
