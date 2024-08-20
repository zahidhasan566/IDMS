<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealearInvoiceDocument extends Model
{
    use HasFactory;
    protected $table = "DealearInvoiceDocument";
    public $timestamps = false;
    public $primaryKey = 'ReceiveId';
    public $incrementing = true;
    protected $keyType = "string";
    protected $guarded = [];
}
