<?php

namespace App\Models\JobCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YtdFiNoStatus extends Model
{
    use HasFactory;

    protected $table = "YtdNoStatusReason";
    public $timestamps = false;
    public $primaryKey = 'Id';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}