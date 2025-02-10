<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerStock extends Model
{
    use HasFactory;

    protected $table = "DealarStock";
    public $timestamps = false;
    public $primaryKey = false;
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
