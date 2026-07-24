<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $primaryKey = 'evidenceID';

    protected $fillable = [
        'complaintID',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_time'
    ];
}
