<?php

namespace App\Models\Sap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapUserLog extends Model
{
    use HasFactory;

    protected $table = "SapUserLog";
    public $primaryKey = 'Id';
    protected $guarded = [];
    public $timestamps = false;
}
