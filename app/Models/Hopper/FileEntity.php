<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;


class FileEntity extends Model
{
    protected $fillable = array(
        'event_session_id',
        'filename',
        'mime',
        'session_id',
        'storage_disk',
        'path',
        'status',
        'data',
        'history',
        'filename_history',
    );
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_session_id' => 'integer',
        'history' => 'array',
        'filename_history' => 'array',
        
    ];
    
    public function event_session()
    {
        return $this->belongsTo('App\Models\Hopper\EventSession');
    }
}
