<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanMaster extends Model
{
    use HasFactory;
    protected $table = "ChallanMaster";
    public $timestamps = false;
    public $primaryKey = 'ChallanNo';
    protected $guarded = [];
}
