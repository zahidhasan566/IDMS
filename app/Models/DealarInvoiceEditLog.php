<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealarInvoiceEditLog extends Model
{
    use HasFactory;

    protected $table = "DealarInvoiceEditLog";
    public $primaryKey = "Id";
    public $timestamps = false;
}
