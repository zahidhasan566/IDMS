<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = "Invoice";
    public $primaryKey = 'InvoiceNo';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';

    public function details()
    {
        return $this->hasMany(InvoiceDetails::class,'Invoiceno','InvoiceNo');
    }
}
