<?php

namespace App\Models\JobCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSIQuestion extends Model
{
    use HasFactory;
    protected $table = "CSIQuestion";
    public $timestamps = false;
    public $primaryKey = 'QuestionID';
    protected $guarded = [];
}
