<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousBikeUsage extends Model
{
    use HasFactory;

    protected $table = "PreviousBikeUsage";
    public $primaryKey = 'BikeUsage';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
