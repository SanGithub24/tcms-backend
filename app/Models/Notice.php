<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notice extends Model
{
    protected $primaryKey = 'noticeID';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'status',
        'category',
        'priority',
        'location',
        'is_featured',
        'expires_at',
        'image'
        // 'image_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'userID');
    }
}