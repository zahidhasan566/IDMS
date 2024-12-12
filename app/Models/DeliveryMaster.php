<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMaster extends Model
{
    use HasFactory;

    protected $table = "DeliveryChecklistMaster";
    public $primaryKey = 'InquiryId';
    protected $guarded = [];
    public $timestamps = false;
}
