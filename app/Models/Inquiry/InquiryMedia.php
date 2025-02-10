<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryMedia extends Model
{
    use HasFactory;

    protected $table = "InquiryMedia";
    public $timestamps = false;
    public $primaryKey = 'InquiryMediaId';
    public $incrementing = true;
    protected $guarded = [];
}
