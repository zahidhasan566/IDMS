<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $connection = "sqlsrv_user_log";
    protected $table = "UserLog";
    public $timestamps = false;
    public $primaryKey = false;
    public $incrementing = false;
    protected $guarded = [];
}
