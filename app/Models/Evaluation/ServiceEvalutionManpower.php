<?php

namespace App\Models\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEvalutionManpower extends Model
{
    use HasFactory;
    protected $table = "ServiceEvalutionManpower";
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = "string";
    protected $guarded = [];
}
