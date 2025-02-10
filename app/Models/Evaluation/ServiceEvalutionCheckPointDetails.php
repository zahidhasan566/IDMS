<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvalutionCheckPointDetails extends Model
{
    use HasFactory;
    protected $table = "ServiceEvalutionCheckPointDetails";
    public $timestamps = false;
    protected $guarded = [];
}
