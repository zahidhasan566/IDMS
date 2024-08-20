<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upazilla extends Model
{
    use HasFactory;

    protected $table = "Upazilla";
    public $primaryKey = 'UpazillaCode';
    public $timestamps = false;
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = "string";
}
