<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = array(
        'event_session_id',
        'file_entity_id',
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
        'history',
		'assignment_user_id',
		'visitor_type',
		'working_filename',
    );
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_session_id' => 'integer',
        'file_entity_id' => 'integer',
        'history' => 'array',    
		'assignment_user_id' => 'integer',
    ];

    public function event_session()
    {
        return $this->belongsTo('App\Models\Hopper\EventSession');
    }
    
    public function file_entity()
    {
        return $this->hasOne('App\Models\Hopper\FileEntity', 'id', 'file_entity_id');
    }
	
	public function user()
    {
        return $this->belongsTo('App\Models\Access\User\User', 'assignment_user_id' );
    }
	
	
}
