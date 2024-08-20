<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldBikeModel extends Model
{
    use HasFactory;
    protected $table = "OldBikeModel";
    public $timestamps = false;
    public $primaryKey = 'Id';
    protected $guarded = [];
}
