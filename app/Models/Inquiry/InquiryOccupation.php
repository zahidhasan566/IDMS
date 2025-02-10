<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryOccupation extends Model
{
    use HasFactory;

    protected $table = "InquiryOccupation";
    public $timestamps = false;
    public $primaryKey = 'OccupationId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
