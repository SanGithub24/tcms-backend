<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintAssignment extends Model
{
    protected $primaryKey = 'assignmentID';

    protected $fillable = [
        'complaintID',
        'userID_police',
        'assigned_by_admin',
        'assigned_at',
        'assignment_type',
        'assignment_status',
        'assignment_reason'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaintID', 'complaintID');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'userID_police', 'userID');
    }
}
