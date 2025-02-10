<?php

namespace App\Models\JobCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardCSIDetails extends Model
{
    use HasFactory;
    protected $table = "JobCardCSIDetails";
    public $timestamps = false;
    protected $guarded = [];
}
