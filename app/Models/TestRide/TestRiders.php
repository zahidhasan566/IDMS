<?php

namespace App\Models\TestRide;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRiders extends Model
{
    use HasFactory;
    protected $table = "TestRiders";
    protected $guarded = [];
    public $timestamps = false;
}
