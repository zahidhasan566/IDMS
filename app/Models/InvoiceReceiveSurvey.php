<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReceiveSurvey extends Model
{
    use HasFactory;
    protected $table = "InvoiceReceiveSurvey";
    public $primaryKey = 'SurveyID';
    public $timestamps = false;
    public $incrementing =true;
    protected $guarded = [];
}
