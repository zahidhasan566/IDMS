<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagshipStockAdjustmentMaster extends Model
{
    use HasFactory;
    protected $table = "FlagshipStockAdjustmentMaster";
    public $timestamps = false;
    public $primaryKey = 'Id';
    public $incrementing = false;
    protected $guarded = [];
}
