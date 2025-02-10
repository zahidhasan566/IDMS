<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReceiveSurveyQuestion extends Model
{
    use HasFactory;
    protected $table = "InvoiceReceiveSurveyQuestions";
    public $primaryKey = 'SurveyQuestionID';
    protected $guarded = [];
    public $timestamps = false;
}
