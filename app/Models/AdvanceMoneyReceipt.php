<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceMoneyReceipt extends Model
{
    use HasFactory;

    protected $table = "AdvanceMoneyReceipt";
    public $timestamps = false;
    public $primaryKey = 'MoneyRecNo';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
