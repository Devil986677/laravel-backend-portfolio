<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transaction_track';
    protected $fillable = [
        'userId',
        'transaction_uuid',
        'productId',
        'total_amount',
        'extras',
        'status',
    ];
}
