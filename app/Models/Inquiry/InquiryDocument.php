<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryDocument extends Model
{
    use HasFactory;

    protected $table = "InquiryDocument";
    public $timestamps = false;
    public $primaryKey = 'InquiryDocumentId';
    public $incrementing = true;
    protected $guarded = [];
}
