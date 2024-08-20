<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvalutionDetails extends Model
{
    use HasFactory;
    protected $table = "ServiceEvalutionDetails";
    public $primaryKey = 'EvalutionDetailsId';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
