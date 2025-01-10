<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagshipInvoiceBRTA extends Model
{
    use HasFactory;

    protected $table = "FlagshipInvoiceBRTA";
    public $primaryKey = 'InvoiceNo';
    protected $keyType = 'string';
    protected $guarded = [];
    public $timestamps = false;

}
