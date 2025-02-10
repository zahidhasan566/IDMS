<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = "District";
    public $primaryKey = 'DistrictCode';
    public $timestamps = false;
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = "string";
}
