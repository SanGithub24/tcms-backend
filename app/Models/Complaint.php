<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\Tourist;
use App\Models\Evidence;

class Complaint extends Model
{
    protected $primaryKey = 'complaintID';

    protected $fillable = [
        'touristID',
        'locationID',
        'category',
        'description',
        'incident_date',
        'complaint_date',
        'police_note',
        'status',
        'contact_method',
        'contact_number'
    ];

    protected $appends = ['final_resolution_note'];

    public function getFinalResolutionNoteAttribute()
    {
        if (empty($this->police_note)) {
            return '';
        }

        $blocks = array_filter(array_map('trim', explode("--- INVESTIGATION NOTE ---", $this->police_note)));
        
        if (empty($blocks)) {
            return $this->police_note; // fallback
        }

        $lastBlock = end($blocks);
        
        // Remove the officer/date headers from the final note to show to tourist,
        // or we can just leave it so they see the timestamp and officer name.
        // The user said: "only final invetigation/police note that add by who ever police officer". 
        // Showing the timestamp/officer is fine, or we can strip it. Let's just return the block.
        return $lastBlock;
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationID', 'locationID');
    }

    public function assignments()
    {
        return $this->hasMany(ComplaintAssignment::class, 'complaintID', 'complaintID');
    }

    public function tourist()
    {
        return $this->belongsTo(Tourist::class, 'touristID', 'touristID');
    }

    public function evidence()
    {
        return $this->hasMany(Evidence::class, 'complaintID', 'complaintID');
    }

}
