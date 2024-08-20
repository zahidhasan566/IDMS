<?php

namespace App\Models\Logistics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerDocument extends Model
{
    use HasFactory;
    protected $table = "DealerDocument";
    public $primaryKey = 'DocumentID';
    protected $guarded = [];
    public $timestamps = false;
}
