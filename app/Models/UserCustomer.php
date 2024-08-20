<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCustomer extends Model
{
    use HasFactory;
    protected $table = "UserCustomer";
    public $timestamps = false;
    public $primaryKey = false;
    public $incrementing = false;
    protected $guarded = [];
}
