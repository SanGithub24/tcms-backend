<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'locationID';

    protected $fillable = [
        'complaintID',
        'city',
        'district',
        'province',
        'latitude',
        'longitude',
        'description'
    ];
}
