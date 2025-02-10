<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryMainUser extends Model
{
    use HasFactory;

    protected $table = "InquiryMainUser";
    public $timestamps = false;
    public $primaryKey = 'InquiryMainUserId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
