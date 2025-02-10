<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoiceMaster extends Model
{
    use HasFactory;
    protected $table = "OrderInvoiceMaster";
    public $timestamps = false;
    protected $primaryKey = 'OrderNo';
    protected $guarded = [];

}
