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
        'history',
		'share_internal',
        'share_external',
        'share_recording_internal',
        'share_recording_external'
    );
    
    protected $dates = ['created_at', 'updated_at', 'check_in_datetime'];
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'dates_rooms' => 'object',   
        'history' => 'array',
    ];
	
//	protected $appends = ['session_files'];
    
    /**
    * Get the route key for the model.
    *
    * @return string
    */
    public function getRouteKeyName()
    {
        return 'session_id';
    }
    

    public function visits() {
        return $this->hasMany('App\Models\Hopper\Visit');
    }
    
//    public function file_entity() {
//        return $this->hasOne('App\Models\Hopper\FileEntity');
//    }
	
	/**
     * Get the checked in attribute
     *
     * @param  string  $value
     * @return string
     */
    public function getSessionFilesAttribute($value)
    {
		$hopperfile = new \App\Services\Hopper\HopperFile;
		
		$sessionFiles = $hopperfile->locate($this->attributes['session_id']);
		
		if(empty($sessionFiles)){
			return false;
		}
        
		return $sessionFiles;
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
    
    
//    public function getDatesRoomsAttribute($value)
//    {
//        $value_array = json_decode($value);
//        foreach($value_array as $key => $date_room){
////             debugbar()->info($date_room->date);
//            $value_array[$key]->date = \Carbon\Carbon::parse($date_room->date)->timezone(config('hopper.event_timezone', 'UTC'))->format('m/d/y h:i A');
//        }
//        return $value_array;
//    }
}
