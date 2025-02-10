<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = "ProductPromo";
    public $primaryKey = 'PromoId';
    protected $guarded = [];
    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(ProductPromoDetail::class,'PromoId','PromoId')
            ->join('Product','Product.ProductCode','ProductPromoDetails.ProductCode');
    }
}
