<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoiceDetailsLog extends Model
{
    use HasFactory;
    protected $table = "OrderInvoiceDetailsLog";
    public $primaryKey = 'OrderInvoiceDetailsLog';
    protected $guarded = [];
    public $incrementing = true;
}
