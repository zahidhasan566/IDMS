<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BikeCC extends Model
{
    use HasFactory;

    protected $table = "BikeCC";
    public $primaryKey = 'BikeCC';
    protected $guarded = [];
    public $timestamps = false;
}
