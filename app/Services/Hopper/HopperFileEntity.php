<?php

namespace App\Services\Hopper;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
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
use App\Models\Hopper\FileEntity;

use App\Services\Hopper\Hopper;
use App\Services\Hopper\HopperDBX;

//use App\Jobs\Hopper\CopyFile;

class HopperFileEntity extends HopperFile{
    
    use DispatchesJobs;
    
    
//    public $hopper_temporary_name;
//    public $hopper_working_name;
//    public $hopper_master_name;
//    public $hopper_archive_name;
    
    
    public function show($id = null){
        if(empty($id)){
            return false;
        }
        
    }

    public function edit(FileEntity $FileEntity){
        
        $data = [];
        $hopper = new Hopper();
        $currentVersion = $this->getCurrentVersion($FileEntity->filename);
        $nextVersion = $currentVersion + 1;
        $GroupedFileHistory = $hopper->groupedHistory($FileEntity->history);
        
        $data = [
            'FileEntity' => $FileEntity,
            'currentVersion' => $currentVersion,
            'nextVersion' => $nextVersion,
            'GroupedFileHistory' => $GroupedFileHistory,
        ];
        
        if(count($FileEntity->event_session)){
            $data['EventSession'] = $FileEntity->event_session;
        }
        
        return $data;
    }

    
    public function store($data = []) {
        
        if(!empty($data)){
            
            $this->copyTemporaryNewFileToMaster($data['filename']);
        
            $FileEntity = FileEntity::create($data);
        
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'create', 'Created', $FileEntity->filename));
            return $FileEntity;
        }
        
        return false;
    }

    public function update(Request $request, FileEntity $FileEntity){
        
         switch ($request->action){
            default:
                
                break;
         }
        
        $FileEntity->update($request->all());
        
        //$id, $event, $notes = '', $filename = '', $tasks = [], $user = null, $request = null
        event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'update', 'Updated', null, null));
        
        if(isset($request->currentfilename) && isset($request->filename) && ($request->currentfilename !== $request->filename) ){
            $this->copyTemporaryNewFileToMaster($request->filename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'copy', 'Moved '.$request->filename.' to master', null, $request->filename));
            $this->moveMasterToArchive($request->currentfilename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'move', 'Moved '.$request->currentfilename.' to archive', null, $request->currentfilename));
        }
        
        return $FileEntity;
        
    }
    
}