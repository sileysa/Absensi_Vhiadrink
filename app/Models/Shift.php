<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'checkin_start',
        'checkin_end',
        'checkout_start',
        'checkout_end',
        'checkout_time',
        'is_active',
    ];
}