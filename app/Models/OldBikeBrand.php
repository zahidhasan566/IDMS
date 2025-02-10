<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldBikeBrand extends Model
{
    use HasFactory;

    protected $table = "OldBikeBrand";
    public $timestamps = false;
    public $primaryKey = 'Id';
    protected $guarded = [];
}
