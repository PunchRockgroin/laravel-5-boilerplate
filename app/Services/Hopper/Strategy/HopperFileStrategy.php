<?php

namespace App\Services\Hopper\Strategy;

use Illuminate\Foundation\Bus\DispatchesJobs;
//use Symfony\Component\Process\Process;
//use Symfony\Component\Process\ProcessBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Validator;
use Event;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;
use Vinkla\Pusher\PusherManager;
//use App\Models\Hopper\EventSession;
//use App\Models\Hopper\Visit;

use App\Services\Hopper\HopperFile;
use App\Models\Hopper\FileEntity;
use App\Services\Hopper\HopperDBX;
//use App\Jobs\Hopper\CopyFile;
use App\Services\Hopper\Contracts\HopperFileContract;

class HopperFileStrategy extends HopperFile{
    
    
    
    function __construct() {
        parent::__construct();
    }
    
    
    //Visitor comes in, new file
    public function update_session_new_file($request){
        //
		$this->uploadToTemporary($file, $newFileName);
        
        
    }
    
    //Visitor comes in, no new file
    public function update_session_new_file_false($request){
        
        
        
    }
    
    //No Visitor, but new file (blind file update)
    public function update_session_new_file_blind($request){
        
        
        
    }
    
    //File updated by Graphic Operator
    public function update_session_graphic_operator($request){
        
        
        
    }
    
    //File not updated by Graphic Operator
    public function update_session_general($request){
        
        
        
    }
}