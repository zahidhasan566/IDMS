<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoiceDetails extends Model
{
    use HasFactory;
    protected $table = "OrderInvoiceDetails";
    public $timestamps = false;
    protected $primaryKey = 'OrderNo';
    public $incrementing = false;
    protected $guarded = [];

}
