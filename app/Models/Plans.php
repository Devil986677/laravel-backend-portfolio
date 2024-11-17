<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    use HasFactory;
    protected $table = 'plans';
    protected $fillable = [
        'name',
        'description',
        'status',
        'time_duration',
        'support_type',
        'price'
    ];
}
