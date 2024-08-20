<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIntroducingMedia extends Model
{
    use HasFactory;

    protected $table = "ProductIntroducingMedia";
    public $primaryKey = 'Media';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';
}
