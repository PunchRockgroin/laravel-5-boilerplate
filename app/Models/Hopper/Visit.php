<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = array(
        'event_session_id',
        'session_id',
        'checkin_username',
        'visitors',
        'filename_uploaded',
        'file_renamed_to',
        'updates_made',
        'checkin_notes',
        'design_username',
        'difficulty',
        'design_notes',
    );

    public function event_session()
    {
        return $this->belongsTo('App\EventSession');
    }
}
