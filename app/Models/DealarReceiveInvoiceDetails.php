<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarReceiveInvoiceDetails extends Model
{
    use HasFactory;

    protected $table = "DealarReceiveInvoiceDetails";
    public $primaryKey = 'ReceiveDetailsId';
    protected $guarded = [];
    public $timestamps = false;
    // protected $keyType = 'string';
}
