<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvaluationHead extends Model
{
    use HasFactory;
    protected $table = "ServiceEvalutionHead";
    public $timestamps = false;
    public $primaryKey = 'ServiceHeadID';
    protected $guarded = [];

}
