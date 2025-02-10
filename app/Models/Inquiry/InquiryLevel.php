<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryLevel extends Model
{
    use HasFactory;

    protected $table = "InquiryLevel";
    public $timestamps = false;
    public $primaryKey = 'InquiryLevelId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
