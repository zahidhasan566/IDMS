<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationMethod extends Model
{
    use HasFactory;
    protected $table = "EvalutionMethod";
    public $timestamps = false;
    public $primaryKey = 'EvalutionMethodId';
    protected $guarded = [];
}

