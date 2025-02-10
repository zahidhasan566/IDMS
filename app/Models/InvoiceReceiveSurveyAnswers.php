<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReceiveSurveyAnswers extends Model
{
    use HasFactory;
    protected $table = "InvoiceReceiveSurveyAnswers";
    public $primaryKey = 'SurveyAnswerID';
    protected $guarded = [];
    public $timestamps = false;
}
