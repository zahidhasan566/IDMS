<?php

namespace App\Models\TestRide;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRideAgents extends Model
{
    use HasFactory;
    protected $table = "TestRideAgents";
    protected $guarded = [];
    public $timestamps = false;
}
