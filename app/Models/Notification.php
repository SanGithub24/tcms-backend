<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [

        'userID',
        'complaintID',
        'title',
        'message',
        'is_read',

    ];
}