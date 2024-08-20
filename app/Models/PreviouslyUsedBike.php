<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviouslyUsedBike extends Model
{
    use HasFactory;

    protected $table = "PopularBike";
    public $primaryKey = 'BikeName';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
