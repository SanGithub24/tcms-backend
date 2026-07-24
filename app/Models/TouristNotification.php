<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristNotification extends Model
{
    protected $table = 'tourist_notifications';

    protected $fillable = [
        'touristID',
        'complaintID',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function complaint()
    {
        return $this->belongsTo(\App\Models\Complaint::class, 'complaintID', 'complaintID');
    }

    public function tourist()
    {
        return $this->belongsTo(\App\Models\Tourist::class, 'touristID', 'touristID');
    }
}