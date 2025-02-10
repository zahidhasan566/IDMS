<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestInProduct extends Model
{
    use HasFactory;

    protected $table = "InterestInProduct";
    public $primaryKey = 'InterestInProduct';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
