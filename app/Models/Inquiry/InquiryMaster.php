<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryMaster extends Model
{
    use HasFactory;

    protected $table = "InquiryMaster";
    public $timestamps = false;
    public $primaryKey = 'InquiryId';
    public $incrementing = true;
    protected $keyType = "string";
    protected $guarded = [];
}
