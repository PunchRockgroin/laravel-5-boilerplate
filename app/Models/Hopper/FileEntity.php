<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;


class FileEntity extends Model
{
    protected $fillable = array(
        'filename',
        'mime',
        'session_id',
        'storage_disk',
        'path',
        'status',
        'metadata',
        'history',
        'filename_history',
    );
    
    public function event_session()
    {
        return $this->belongsTo('App\EventSession');
    }
}
