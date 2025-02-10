<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv_eps";
    public $timestamps = false;
    public $primaryKey = false;
    protected $guarded = [];
    public $incrementing = false;
    protected $table = "Depot";
}
