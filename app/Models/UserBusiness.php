<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBusiness extends Model
{
    use HasFactory;
    protected $table =  "UserBusiness";
    public $timestamps = false;
    public $primaryKey = "UserId";
    public $incrementing = false;
    protected $guarded = [];
}
