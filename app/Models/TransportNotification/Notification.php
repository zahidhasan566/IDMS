<?php

namespace App\Models\TransportNotification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = "TransportNotification";
    public $timestamps = false;
    public $primaryKey = 'NotificationID';
    protected $guarded = [];
}
