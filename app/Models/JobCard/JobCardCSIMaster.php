<?php

namespace App\Models\JobCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardCSIMaster extends Model
{
    use HasFactory;
    protected $table = "JobCardCSIMaster";
    public $timestamps = false;
    public $primaryKey = 'CSIMasterID';
    protected $guarded = [];
}
