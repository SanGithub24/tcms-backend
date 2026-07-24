<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $primaryKey = 'emailID';

    protected $fillable = [
        'complaintID',
        'recipient_email',
        'subject',
        'sent_status',
        'email_type',
        'sent_at'
    ];
}