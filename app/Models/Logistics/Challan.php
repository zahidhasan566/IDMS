<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    use HasFactory;

    protected $table = "Challan";
    public $primaryKey = 'ChallanID';
    protected $guarded = [];
    public $timestamps = false;
}
