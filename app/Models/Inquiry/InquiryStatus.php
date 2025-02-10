<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryStatus extends Model
{
    use HasFactory;

    protected $table = "InquiryStatus";
    public $timestamps = false;
    public $primaryKey = 'InquiryStatusId';
    public $incrementing = true;
    protected $keyType = "string";
    protected $guarded = [];
}
