<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitorCompany extends Model
{
    use HasFactory;
    protected $table = "CompetitorCompany";
    public $primaryKey = 'CompanyID';
    protected $guarded = [];
    public $timestamps = false;
}
