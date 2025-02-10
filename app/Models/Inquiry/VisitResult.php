<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitResult extends Model
{
    use HasFactory;

    protected $table = "VisitResult";
    public $timestamps = false;
    public $primaryKey = 'VisitResultId';
    public $incrementing = true;
    protected $guarded = [];
}
