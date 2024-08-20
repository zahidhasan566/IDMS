<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "Product";
    public $primaryKey = 'ProductCode';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';

    public function dealerstock(){
        return $this->belongsTo(DealerStock::class,'ProductCode','ProductCode');
    }

}
