<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarWarrantyClaim extends Model
{
    use HasFactory;

    protected $table = "DealarWarrantyClaim";
    public $primaryKey = 'DCWarrantyID';
    public $timestamps = false;
    protected $guarded = [];
}
