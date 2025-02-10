<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvaluationDetails extends Model
{
    use HasFactory;

    protected $table = "ServiceEvalutionDetails";
    public $timestamps = false;
    protected $guarded = [];
}
