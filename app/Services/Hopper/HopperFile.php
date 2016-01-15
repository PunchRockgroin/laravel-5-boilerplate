<?php
namespace App\Services\Hopper;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Validator;
use Event;

use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;
use Vinkla\Pusher\PusherManager;

use App\Models\Hopper\EventSession;
use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;

class HopperFile {
    
    protected $storagepath;
    protected $pusher;
    protected $driver_storage_path;
    
    protected $hopper_temporary_name;
    
    function __construct() {
        $this->storagepath = config('hopper.local_storage');    
        $this->driver_storage_path = $this->getDriverStoragePath();
        
        $this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
        $this->hopper_working_name = env('HOPPER_WORKING_NAME', 'Working/');
        $this->hopper_master_name = env('HOPPER_MASTER_NAME', '1_Master/');
    }
    
    
    public function getDriverStoragePath($disk = 'hopper'){
        return Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();
    }
    
    
    public function validateFile($request){
        $rules = array(
            'file' => 'mimes:'.config('hopper.checkin_upload_mimes'),
        );

        $messages = [
            'mimes' => 'Invalid file type or corrupt file',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return $validation->errors();
        }else{
            return true;
        }
    }

    public function uploadToTemporary($file, $newFileName) {
        try
        {
          $upload_success = Storage::disk('hopper')->put($this->hopper_temporary_name . $newFileName, $file);
           $filemeta = Storage::disk('hopper')->getMetaData($this->hopper_temporary_name . $newFileName);
           $filemeta['storage_disk'] = 'hopper';
           $filemeta['mime'] = Storage::disk('hopper')->mimeType( $this->hopper_temporary_name . $newFileName);;          
           return $filemeta;
        }
        catch(Exception $e)
        {
          return $e;
        }

    }
    
    public function uploadToDone($file, $newFileName) {
        $fd = fopen($file->getRealPath(), 'r+');
        $upload_success = Storage::disk('hopper')
            ->put(env('HOPPER_DONE_NAME', 'DONE/')  . $newFileName, $fd);
        fclose($fd);
        if ($upload_success) {
            return true;
        }
        return false;
    }
    
    public function moveTemporaryNewFileToWorking($newFileName) {
        if (config('hopper.working_storage') === 'dropbox') {
            return $this->_moveDBXTemporaryToDBXWorking($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $dbWorking = $this->_moveDBXTemporaryToDBXWorking($newFileName);
            }
            return $this->_moveHopperTemporaryToHopperWorking($newFileName);
        }
    }
    
    public function _moveHopperTemporaryToHopperWorking($newFileName) {
        $newFile_exists = Storage::disk('hopper')->exists($this->hopper_temporary_name . $newFileName);
        $newFile_exists_in_working = Storage::disk('hopper')->exists($this->hopper_working_name . $newFileName);
        //If there is a new file name and it exists in temporary and not in working
        if ($newFileName && $newFile_exists && !$newFile_exists_in_working) {
            //Move that file out of Temporary and into Working
            Storage::disk('hopper')
                    ->copy($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName);

            return $newFileName;
        } elseif ($newFileName && $newFile_exists && $newFile_exists_in_working) { //If there is a new file name and it exists in temporary and in working
            // Delete the File in Working
            if (Storage::disk('hopper')->delete($this->hopper_working_name . $newFileName)) {
                //Copy the temporary file over to Working
                Storage::disk('hopper')
                        ->copy($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName);
            }
            
        } else {
            return false;
        }
    }
    
    public function _moveDBXTemporaryToDBXWorking($newFileName) {
        if (!config('hopper.dropbox_enable')) {
            return false;
        }
        Dropbox::createFolder('/Working');
        //Does the file exist in temporary
        $fileExists = Dropbox::getMetadata('/temporary/' . $newFileName);
        //Does the file exist in temporary
        $fileExists_in_working = Dropbox::getMetadata('/Working/' . $newFileName);
//        debugbar()->info($fileExists);
        //If the file exists and does't exist in working
        if ($fileExists !== null && $fileExists_in_working === null) {
            //Move the file from temporary to working
            $dropboxData = Dropbox::move('/temporary/' . $newFileName, '/Working/' . $newFileName);
//            \Log::info('File Transfered To Dropbox: ' . $newFileName);

            return $dropboxData;
        } elseif ($fileExists !== null && $fileExists_in_working !== null) { //If the file exists and does exist in working
             //Delete the file first
            $deletedFile = Dropbox::delete('/Working/' . $newFileName);
            //If it's deleted
            if ($deletedFile['is_deleted']) {
                //Move the file from temporary to working
                $dropboxData = Dropbox::move('/temporary/' . $newFileName, '/Working/' . $newFileName);
//                \Log::info('File Transfered To Dropbox: ' . $newFileName);
            }
            return $dropboxData;
        }
        return false;
    }
    
    public function moveDBXTemporaryToWorking($newFileName) {
        if (!config('hopper.dropbox_enable')) {
            return false;
        }
        return $this->_moveDBXTemporaryToDBXWorking($newFileName);
    }

    public function copyTemporaryNewFileToMaster($newFileName) {
        if (config('hopper.master_storage') === 'dropbox') {
            return $this->_copyDBXTemporaryToDBXMaster($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $dbMaster = $this->_copyDBXTemporaryToDBXMaster($newFileName);
            }
            return $this->_copyHopperTemporaryNewFileToHopperMaster($newFileName);
        }
    }

    public function _copyHopperTemporaryNewFileToHopperMaster($newFileName) {
        $newFile_exists = Storage::disk('hopper')->exists($this->hopper_temporary_name . $newFileName);
        $newFile_exists_in_master = Storage::disk('hopper')->exists($this->hopper_master_name . $newFileName);
        //If there is a new file name and it exists in Temporary and not in Master
        if ($newFileName && $newFile_exists && !$newFile_exists_in_master) {
             //Copy the file from Temporary to master
            event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_temporary_name. $newFileName, $this->hopper_master_name . $newFileName)); 
            return $newFileName;
        } elseif ($newFile_exists && $newFile_exists_in_master) {  //If there is a new file name and it exists in Temporary and in Master
            //Delete the file from master	
            if (Storage::disk('hopper')->delete($this->hopper_master_name. $newFileName)) {
                //Copy the file from Temporary to Master
                event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)); 
            }
        } else {
            return false;
        }
    }

    public function _copyDBXTemporaryToDBXMaster($newFileName) {
        if (!config('hopper.dropbox_enable')) {
            return false;
        }
        $newFile_exists = Dropbox::getMetadata('/temporary/' . $newFileName);
        $newFile_exists_in_master = Dropbox::getMetadata('/1_Master/' . $newFileName);
        //If there is a new file name and it exists in Temporary and not in Master
        if ($newFileName && $newFile_exists !== null && $newFile_exists_in_master === null) {
            //Copy the file from Temporary to Master
            $dropboxData = Dropbox::copy('/temporary/' . $newFileName, '/1_Master/' . $newFileName);
            return $dropboxData;
        } elseif ($newFile_exists !== null && $newFile_exists_in_master !== null) {  //If there is a new file name and it exists in Temporary and in Master
             //Delete the file from master
            $deletedFile = Dropbox::delete('/1_Master/' . $newFileName);
            if ($deletedFile['is_deleted']) {  //If the file is deleted
                //Copy the file from Temporary to Master
                $dropboxData = Dropbox::copy('/temporary/' . $newFileName, '/1_Master/' . $newFileName);
                return $dropboxData;
            }
        }
        if ($dropboxData !== null) {
            \Log::info('File Transfered from Dropbox Temporary to Dropbox Master: ' . $dropboxData['path']);
        }
        return false;
    }

    public function copyDBXTemporaryToDBXMaster($newFileName) {
        if (!config('hopper.dropbox_enable')) {
            return false;
        }
        return $this->_copyDBXTemporaryToDBXMaster($newFileName);
    }

    public function copyMasterToWorking($currentMaster, $updateVersionTo = false) {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $dbMaster = $this->_copyMasterDBXToWorkingDBX($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $dbMaster = $this->_copyMasterDBXToWorkingDBX($currentMaster, $updateVersionTo);
            }
//            event(new \App\Events\Backend\Hopper\MasterUpdated($oldFilePath, $newFilePath, $uuid = null));
            $newMaster = $this->_copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo = false) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        $master_exists_in_working = Storage::disk('hopper')->exists($this->hopper_working_name . $currentMaster);
        $fileData = [];
        $fileData = $currentMaster;
        //If we are updating Master Version and the Master Exists       
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            //Check if the new master exists in working
            $newFileName_exists_in_working = Storage::disk('hopper')->exists($this->hopper_working_name . $newFileName);
            //If the new master exists in working
			//Delete the master in Working
            if ($newFileName_exists_in_working && Storage::disk('hopper')->delete($this->hopper_working_name . $newFileName)) {	
                //Copy the master over
                event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            } else { //If the new master does not exist
                //Copy the Master Over
		event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            }
            $fileData = $newFileName;
        }
        if ($master_exists) { //If the Master exists in Master
            //If the Master file exists in Working
            if ($master_exists_in_working) {
                //Delete the master in Working
                if (Storage::disk('hopper')->delete($this->hopper_working_name . $currentMaster)) {
                    //Copy the master over
                    event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
                }
            } else {//If the Master file doesnt exist in Working
                //Copy the master over
		event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
            }
            

            
        } else {
            return false;
        }
        return $fileData;
    }

    public function _copyMasterDBXToWorkingDBX($currentMaster, $updateVersionTo = false) {
        $master_exists = Dropbox::getMetadata('/1_Master/' . $currentMaster);
        $master_exists_in_working = Dropbox::getMetadata('/working/' . $currentMaster);
        //If we are updating Master Version and the Master Exists    
        if ($updateVersionTo && $master_exists !== null) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
             //Check if the old Master exists in Working
            if (Dropbox::getMetadata('/working/' . $currentMaster) !== null) { 
                 //Delete the master in Working
                $deletedFile = Dropbox::delete('/working/' . $currentMaster);
                if ($deletedFile['is_deleted']) {
                    //Copy the master over
                    $dropboxData['old_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $currentMaster);
                    return $dropboxData;
                }
            }else{
                $dropboxData['old_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $currentMaster);
            }
            //Check if the new Master exists in Working
            if (Dropbox::getMetadata('/working/' . $newFileName) !== null) {
                //Delete the master in Working
                $deletedFile = Dropbox::delete('/working/' . $newFileName);
                if ($deletedFile['is_deleted']) {
                    $dropboxData['new_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $newFileName);
                    return $dropboxData;
                }
            }else{
                $dropboxData['new_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $newFileName);
            }

            return $newFileName;
        } else if (($master_exists !== null)) {
            if (Dropbox::getMetadata('/working/' . $currentMaster) !== null) {
                $deletedFile = Dropbox::delete('/working/' . $currentMaster);
                if ($deletedFile['is_deleted']) {
                    $dropboxData['old_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $currentMaster);
                    return $dropboxData;
                }
            }else{
                $dropboxData['old_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/working/' . $currentMaster);
            }
            return $dropboxData;
        } else {
            return false;
        }
        return false;
    }

    public function copyDBXMasterToWorking($currentMaster, $updateVersionTo = false) {
        if (!config('hopper.dropbox_enable')) {
            return false;
        }
        $newMaster = $this->_copyMasterDBXToWorkingDBX($currentMaster, $updateVersionTo);
        return $newMaster;
    }

    public function copyMasterToMaster($currentMaster, $updateVersionTo = false) {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $dbMaster = $this->_copyMasterDBXToMasterDBX($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $dbMaster = $this->_copyMasterDBXToMasterDBX($currentMaster, $updateVersionTo);
//                debugbar()->info($dbMaster);
            }
            $newMaster = $this->_copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo = false) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        //If we are updating Master Version and the Master Exists    
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
			
            $renamed_master_exists_in_master = Storage::disk('hopper')->exists($this->hopper_master_name . $newFileName);
//			debugbar()->info('Update'); 
			if ($renamed_master_exists_in_master && Storage::disk('hopper')->delete($this->hopper_master_name  . $newFileName) ) {
				event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_master_name  . $newFileName));
            } else {
                //Copy the Current Master to a new filename
		event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_master_name  . $newFileName));						 
            }
            return $newFileName;
        } else {
			
            return false;
        }
    }

    public function _copyMasterDBXToMasterDBX($currentMaster, $updateVersionTo = false) {
        $master_exists = Dropbox::getMetadata('/1_Master/' . $currentMaster);
        $master_exists_in_working = Dropbox::getMetadata('/working/' . $currentMaster);
        //If we are updating Master Version and the Master Exists  
        if ($updateVersionTo && $master_exists !== null) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            //Check if the new Master exists in Master
            if (Dropbox::getMetadata('/1_Master/' . $newFileName) !== null) {
                //Don't do anything
                return true;
//                }
            }else{
                $dropboxData['new_master'] = Dropbox::copy('/1_Master/' . $currentMaster, '/1_Master/' . $newFileName);
            }

            return $newFileName;
        }
        return false;
    }
    
    
    public static function getCurrentVersion($currentFileName) {
        $currentFileParts = pathinfo($currentFileName)['filename'];
        $currentFileNameParts = explode('_', $currentFileParts);
    		if(empty($currentFileNameParts)){
    			return false;
    		}
        //If there is no file in Master but placeholer LCCNOFILE is there
        if($currentFileNameParts[3] === 'LCCNOFILE'){
            //The Next version is 7
            $currentVersion = 7;
        }else{ //Do the usual thing
            $currentVersion = (int) preg_replace("/[^0-9]/", '', $currentFileNameParts[3]);
        }
        return str_pad($currentVersion, 2, '0', STR_PAD_LEFT);
    }
    
    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null) {
		
        $currentFileParts = pathinfo($currentFileName)['filename'];
		if($currentFileExtension === null){
			$currentFileExtension = pathinfo($currentFileName)['extension'];
		}
//        
        $currentFileNameParts = explode('_', $currentFileParts);
        $currentFileNameArray = [
            'sessionID' => $currentFileNameParts[0],
            'speaker' => $currentFileNameParts[1],
            'roomIDs' => $currentFileNameParts[2],
            'version' => $currentFileNameParts[3],
            'shareStatus' => $currentFileNameParts[4]
        ];

        $newFileName = $currentFileNameArray['sessionID']
                . '_' . $currentFileNameArray['speaker']
                . '_' . $currentFileNameArray['roomIDs']
                . '_LCC' . $nextVersion
                . '_' . $currentFileNameArray['shareStatus']
                . '.' . $currentFileExtension;

        return $newFileName;
    }
}