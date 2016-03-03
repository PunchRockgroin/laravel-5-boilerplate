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

use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\HopperDBX;

//use App\Jobs\Hopper\CopyFile;

class HopperFileEntity extends HopperFile{
    
    use DispatchesJobs;
    
    
//    public $hopper_temporary_name;
//    public $hopper_working_name;
//    public $hopper_master_name;
//    public $hopper_archive_name;
    
    
    protected $hopper;

    /**
     * @param HopperContract    $hopper
     */
    public function __construct(
    HopperContract $hopper
    ) {
        $this->hopper = $hopper;
        
    }
    
    
    
    public function show($id = null){
        if(empty($id)){
            return false;
        }
        
    }

    public function edit(FileEntity $FileEntity){
                
        $currentVersion = $this->hopper->getCurrentVersion($FileEntity->filename);
        $nextVersion = $currentVersion + 1;
        
        $data = [
            'FileEntity' => $FileEntity,
            'currentVersion' => $currentVersion,
            'nextVersion' => $nextVersion,
            'History' => $this->hopper->groupedHistory($FileEntity->history),
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
            return $FileEntity;
        }
        
        return false;
    }

    public function update(Request $request, FileEntity $FileEntity){
        
        $FileEntity = $this->_update($request->all());
        
        if(isset($request->currentfilename) && isset($request->filename) && ($request->currentfilename !== $request->filename) ){
            $path = $this->copyTemporaryNewFileToMaster($request->filename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'copy', 'Moved '.$request->filename.' to master', $request->filename, ['update_path' => $path]));
            $this->moveMasterToArchive($request->currentfilename);
            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'move', 'Moved '.$request->currentfilename.' to archive', null, $request->currentfilename));
        }
//        elseif(isset($request->behavior) && isset($request->filename) && $request->behavior === 'update_visit'){
//            $this->copyTemporaryNewFileToMaster($request->filename);
//            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'copy', 'Moved updated visit file '.$request->filename.' to master', null, $request->filename));
//            $this->moveMasterToArchive($request->filename);
//            event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'move', 'Moved outdated '.$request->filename.' to archive', null, $request->filename));
//        }
        
        return $FileEntity;
        
    }
    
    public function _update($data = [], FileEntity $FileEntity){
        $FileEntity->update($data);
        event(new \App\Events\Backend\Hopper\FileEntityUpdated($FileEntity->id, 'update', 'Updated', null, null));
        return $FileEntity;
    }
    
    public function parseForExport($FileEntities){
        if($FileEntities->isEmpty()){
            return $FileEntities;
        }
         
        foreach($FileEntities as $key => $FileEntity){
            unset($FileEntities[$key]->id);
            unset($FileEntities[$key]->history);       
        }
        return $FileEntities;            
    }
    
    
    public function import(){
        $count = 0;
        $hopperfile = new \App\Services\Hopper\HopperFile();
        $filesInMaster = $hopperfile->getAllInMaster();
        $filesInMaster = $hopperfile->mapFileMeta($filesInMaster);

        $filesInMasterChunk = $filesInMaster->chunk(2);

        foreach($filesInMasterChunk as $chunk){
            foreach($chunk as $newFileEntity){
               $eventsession = null;
               $fileentity = FileEntity::firstOrNew(['filename' => $newFileEntity['filename']]);
               $fileentity->fill($newFileEntity);
               //Is there an event session available based on filename;
               $fileparts = $this->getFileParts($fileentity->filename);
               //Is this a legit file
               if(isset($fileparts['sessionID'])){
                   $eventsession = \App\Models\Hopper\EventSession::where('session_id', $fileparts['sessionID'])->first();            
               }else{
                   //not legit, leave loop
                   break;
               }
               if($eventsession){
                   $fileentity->fill([
                       'event_session_id' => $eventsession->id,
                       'session_id' => $eventsession->session_id
                   ]);
               }
               $fileentity->save();
               $count++;
            }
        }
        
        return $count;
    }
    
}