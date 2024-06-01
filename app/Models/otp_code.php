<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class otp_code extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone_number',
        'otp_code'
    ];
}
