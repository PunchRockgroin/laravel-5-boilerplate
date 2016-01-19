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
        'history' => 'array',
        'filename_history' => 'array',
    ];
    
    public function event_session()
    {
        return $this->belongsTo('App\EventSession');
    }
}
