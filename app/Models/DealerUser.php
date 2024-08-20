<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerUser extends Model
{
    use HasFactory;

    protected $table = 'DealerPermission';
    public $timestamps = false;
    public $primaryKey = false;
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
