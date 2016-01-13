<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;

class EventSession extends Model
{
    //
    protected $fillable = array(
        'session_id',
        'checked_in',
        'speakers',
        'onsite_phone',
        'current_file',
        'approval_brand',
        'approval_revrec',
        'approval_legal',
        'dates_rooms',
        'presentation_owner',
        'check_in_datetime',
    );
    
    protected $dates = ['created_at', 'updated_at', 'check_in_datetime'];
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'dates_rooms' => 'object',
    ];
    

    public function visits() {
        return $this->hasMany('App\Visit');
    }
    
    public function file_entity() {
        return $this->hasOne('App\FileEntity');
    }

    
    /**
     * Get the checked in attribute
     *
     * @param  string  $value
     * @return string
     */
    public function getCheckedInAttribute($value)
    {
        return empty($value) ? "NO" : "YES";
    }
    
    public function setCheckedInAttribute($value){
        if (is_bool($value) !== true) {
            $this->attributes['checked_in'] = ($value === "YES" ? TRUE : FALSE);
        }
    }
    
    public function checkedInBoolean(){
        return (boolval($this->attributes['checked_in']));
    }
}
