<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationDesignation extends Model
{
    use HasFactory;
    protected $table = "EvalutionDesignation";
    public $timestamps = false;
    public $primaryKey = 'DesignationId';
    protected $guarded = [];
}
