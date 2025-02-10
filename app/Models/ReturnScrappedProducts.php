<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnScrappedProducts extends Model
{
    use HasFactory;

    protected $table = "ReturnScrappedProducts";
    public $primaryKey = 'ScrapID';
    protected $guarded = [];
    public $timestamps = false;
}
