<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostDocumentDetails extends Model
{
    use HasFactory;

    protected $table = "LostDocumentDetails";
    public $primaryKey = 'LostDocumentID';
    protected $guarded = [];
    public $timestamps = false;
}
