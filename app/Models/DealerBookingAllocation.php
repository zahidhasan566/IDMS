<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerBookingAllocation extends Model
{
    use HasFactory;

    protected $table = "DealerBookingAllocation";
    public $timestamps = false;
    public $primaryKey = 'Id';
    protected $guarded = [];
}
