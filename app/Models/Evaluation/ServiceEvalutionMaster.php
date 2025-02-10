<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvalutionMaster extends Model
{
    use HasFactory;
    protected $table = "ServiceEvalutionMaster";
    public $primaryKey = 'EvalutionId';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = "string";
    protected $guarded = [];

}
