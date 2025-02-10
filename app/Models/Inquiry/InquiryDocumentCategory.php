<?php

namespace App\Models\Inquiry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryDocumentCategory extends Model
{
    use HasFactory;

    protected $table = "InquiryDocumentCategory";
    public $timestamps = false;
    public $primaryKey = 'DocumentId';
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
