<?php

namespace App\Models\stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRackAllocation extends Model
{
    use HasFactory;
    protected $table = "ProductRackAllocation";
    protected $guarded = [];
    public $timestamps = false;
}
