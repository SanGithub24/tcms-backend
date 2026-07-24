<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $primaryKey = 'otpID';

    protected $fillable = [
        'touristID',
        'otp_code',
        'expiry_time',
        'status'
    ];
}
