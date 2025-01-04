<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetails extends Model
{
    use HasFactory;

    protected $table = "DeliveryChecklistDetails";
    public $primaryKey = "ChecklistId";
    protected $guarded = [];
    public $timestamps = false;
}
