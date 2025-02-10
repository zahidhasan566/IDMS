<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyIncome extends Model
{
    use HasFactory;

    protected $table = "MonthlyIncome";
    public $primaryKey = 'IncomeId';
    protected $guarded = [];
    public $timestamps = false;
}
