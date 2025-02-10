<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvalutionRequirmentMaster extends Model
{
    use HasFactory;
    protected $table = "EvalutionRequirmentMaster";
    public $timestamps = false;
    public $primaryKey = 'RequirmentId';
    protected $guarded = [];
}
