<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
    use HasFactory;

    protected $table = "InvoiceDetails";
   // public $primaryKey = '';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
