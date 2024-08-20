<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauseForBuyingNewBike extends Model
{
    use HasFactory;

    protected $table = "CauseForBuyingNewBike";
    public $primaryKey = 'Cause';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
