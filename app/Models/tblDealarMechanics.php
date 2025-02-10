<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tblDealarMechanics extends Model
{
    use HasFactory;

    protected $table = "tblDealarMechanics";
    public $primaryKey = 'MechanicsID';
    public $timestamps = false;
    protected $guarded = [];
//    public $incrementing = false;
}
