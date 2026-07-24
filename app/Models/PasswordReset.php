<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'touristID',
        'otp_code',
        'expiry_time',
        'status'
    ];
}