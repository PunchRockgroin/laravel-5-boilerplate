<?php
namespace App\Services\Hopper;


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
use App\Models\Hopper\FileEntity;

//use App\Jobs\Hopper\CopyFile;

class HopperDBX {
    
    use DispatchesJobs;
    
    protected $storagepath;
    protected $pusher;
    protected $driver_storage_path;
    
    protected $hopper_temporary_name;
    protected $hopper_working_name;
    protected $hopper_master_name;
    protected $hopper_archive_name;
    
    function __construct() {
        
        $this->storagepath = config('hopper.local_storage');    
        $this->driver_storage_path = $this->getDriverStoragePath();
        
        $this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
        $this->hopper_working_name = env('HOPPER_WORKING_NAME', 'Working/');
        $this->hopper_master_name = env('HOPPER_MASTER_NAME', '1_Master/');
        $this->hopper_archive_name = env('HOPPER_ARCHIVE_NAME', 'ZZ_Archive/');
    
        
    }
    
//    public function moveDBXTemporaryToWorking($newFileName) {
//        if (!config('hopper.dropbox_enable')) {
//            return false;
//        }
//        return $this->_moveDBXTemporaryToDBXWorking($newFileName);
//    }
    
    public function moveTemporaryToWorking($newFileName) {
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
    
    
    public function copyTemporaryToMaster($newFileName) {
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
    
    public function copyMasterToWorking($currentMaster, $updateVersionTo = false) {
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
    
    
    public function copyMasterToMaster($currentMaster, $updateVersionTo = false) {
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
    
    
    public function moveMasterToArchive($currentMaster, $updateVersionTo = false) {
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

}

