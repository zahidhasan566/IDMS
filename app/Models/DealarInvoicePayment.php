<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarInvoicePayment extends Model
{
    use HasFactory;

    protected $table = "DealarInvoicePayment";
    public $primaryKey = 'PaymentID';
    protected $guarded = [];
    public $timestamps = false;
    // protected $keyType = 'string';
}
