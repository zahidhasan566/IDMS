<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompititorBrand extends Model
{
    use HasFactory;

    protected $table = "CompititorBrand";
    public $primaryKey = 'BrandCode';
    public $timestamps = false;
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = "string";
}
