<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostDocument extends Model
{
    use HasFactory;

    protected $table = "LostDocument";
    public $primaryKey = 'LostDocumentID';
    protected $guarded = [];
    public $timestamps = false;
}
