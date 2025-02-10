<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryMediaCategory extends Model
{
    use HasFactory;

    protected $table = "InquiryMediaCategory";
    public $timestamps = false;
    public $primaryKey = 'InquiryMediaCategoryId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
