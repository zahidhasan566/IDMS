<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDelivery extends Model
{
    use HasFactory;

    protected $table = "PostDeliveryChecklist";
    public $primaryKey = 'ChecklistId';
    protected $guarded = [];
    public $timestamps = false;
}
