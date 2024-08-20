<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryCustomerCategory extends Model
{
    use HasFactory;

    protected $table = "InquiryCustomerCategory";
    public $timestamps = false;
    public $primaryKey = 'CustomerCategoryId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
