<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvaluationMaster extends Model
{
    use HasFactory;

    protected $table = "ServiceEvalutionMaster";
    public $timestamps = false;
    public $primaryKey = 'EvalutionId';
    protected $guarded = [];
}
