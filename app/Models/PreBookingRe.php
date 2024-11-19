<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreBookingRe extends Model
{
    use HasFactory;
    protected $table = "PreBookingRe";
    public $timestamps = false;
    public $primaryKey = 'Id';
    protected $guarded = [];
}
