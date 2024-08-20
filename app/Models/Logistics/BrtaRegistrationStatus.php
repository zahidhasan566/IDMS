<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrtaRegistrationStatus extends Model
{
    use HasFactory;
    protected $table = "BRTA_RegistrationStatus";
    public $timestamps = false;
    public $primaryKey = 'BRTA_RegistrationStatusID';
    public $incrementing = true;
    protected $guarded = [];
}
