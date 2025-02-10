<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdBrand extends Model
{
    use HasFactory;

    protected $table = "ProdBrand";
    public $primaryKey = 'BrandCode';
    protected $guarded = [];
    public $timestamps = false;
}
