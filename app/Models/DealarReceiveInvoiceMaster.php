<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarReceiveInvoiceMaster extends Model
{
    use HasFactory;

    protected $table = "DealarReceiveInvoiceMaster";
    public $primaryKey = 'ReceiveID';
    protected $guarded = [];
    public $timestamps = false;
   // protected $keyType = 'string';

}
