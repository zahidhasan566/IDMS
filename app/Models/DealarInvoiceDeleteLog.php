<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarInvoiceDeleteLog extends Model
{
    use HasFactory;

    protected $table = "DealarInvoiceDeleteLog";
    public $primaryKey = 'InvoiceID';
    public $timestamps = false;
    protected $guarded = [];
}
