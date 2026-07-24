<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'reviewID';

    protected $fillable = [
        'complaintID',
        'touristID',
        'rating',
        'description',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaintID', 'complaintID');
    }

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'touristID', 'touristID');
    }
}