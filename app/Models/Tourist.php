<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Tourist extends Authenticatable
{
    use HasApiTokens;

    protected $primaryKey = 'touristID';

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'country',
        'password',
        'is_verified'
    ];

    protected $hidden = [
        'password',
    ];
}


// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Tourist extends Model
// {
//     protected $primaryKey = 'touristID';

//     protected $fillable = [
//         'full_name',
//         'email',
//         'phone_number',
//         'country',
//         'password'
//     ];
// }
