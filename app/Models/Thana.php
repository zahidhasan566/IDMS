<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    use HasFactory;

    protected $table = "Thana";
    public $primaryKey = 'ThanaCode';
    public $timestamps = false;
    protected $guarded = [];
    public $incrementing = false;
}
