<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromoDetail extends Model
{
    use HasFactory;

    protected $table = "ProductPromoDetails";
    public $primaryKey = 'ProductPromoDetailsId';
    protected $guarded = [];
    public $timestamps = false;
}
