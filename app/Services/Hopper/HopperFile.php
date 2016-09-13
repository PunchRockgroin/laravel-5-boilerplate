<?php

namespace App\Services\Hopper;

use Illuminate\Foundation\Bus\DispatchesJobs;
//use Symfony\Component\Process\Process;
//use Symfony\Component\Process\ProcessBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Validator;
use Event;
use Cache;
use GrahamCampbell\Dropbox\Facades\Dropbox;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon as Carbon;
use Vinkla\Pusher\PusherManager;
//use App\Models\Hopper\EventSession;
//use App\Models\Hopper\Visit;
use App\Models\Hopper\FileEntity;
use App\Services\Hopper\HopperDBX;
//use App\Jobs\Hopper\CopyFile;
use App\Services\Hopper\Contracts\HopperFileContract;

class HopperFile implements HopperFileContract {

    use DispatchesJobs;

    protected $storagepath;
    protected $pusher;
    protected $driver_storage_path;
    public $hopper_temporary_name;
    public $hopper_relics_name;
    public $hopper_working_name;
    public $hopper_master_name;
    public $hopper_archive_name;

    function __construct() {


        $this->storagepath = config('hopper.local_storage');
        $this->driver_storage_path = $this->getDriverStoragePath();

        $this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
        $this->hopper_relics_name = env('HOPPER_RELICS_NAME', 'relics/');
        $this->hopper_working_name = env('HOPPER_WORKING_NAME', 'working/');
        $this->hopper_master_name = env('HOPPER_MASTER_NAME', '1_Master/');
        $this->hopper_archive_name = env('HOPPER_ARCHIVE_NAME', 'ZZ_Archive/');
    }

    public function getDriverStoragePath($disk = 'hopper') {
        return Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();
    }
	
	public function exists($target, $disk = 'hopper'){
		return Storage::disk($disk)->exists($target);
	}

    public function copyfile($oldFilePath, $newFilePath, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $oldFile_exists = Storage::disk($sourcedisk)->exists($oldFilePath);
        $newFile_exists = Storage::disk($targetdisk)->exists($newFilePath);
        if (!$oldFile_exists) {
            return false;
        }
        //$fd = fopen($this->getDriverStoragePath($disk) . $oldFilePath, "rb");
		$stream = Storage::disk($sourcedisk)->getDriver()->readStream($oldFilePath);
        if ($newFile_exists && Storage::disk($targetdisk)->delete($newFilePath)) {
			if(Storage::disk($targetdisk)->put($newFilePath, $stream)){
				//\Log::info('Copy File: '.$oldFilePath .' to ' . $newFilePath);
				return true;
			}
        } else {
			if(Storage::disk($targetdisk)->put($newFilePath, $stream)){
				//\Log::info('Copy File: '.$oldFilePath .' to ' . $newFilePath);
				return true;
			}
        }	
		//\Log::info('File Operation: '.$oldFilePath .' to ' . $newFilePath);
        return true;
    }

    public function movefile($oldFilePath, $newFilePath, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if ($this->copyfile($oldFilePath, $newFilePath, $sourcedisk, $targetdisk)) {
            //Storage::disk($disk)->delete($oldFilePath);
			if(Storage::disk($targetdisk)->delete($oldFilePath)){
				return true;
			}
        }
    }
	
    public function renamefile($oldFilePath, $newFilePath, $targetdisk = 'hopper') {
			Storage::disk($targetdisk)->move($oldFilePath, $newFilePath);
    }
	
	
	public function locate($query) {

			try { 
				$filesInMaster = $this->getAllInMaster();
				$validFiles = $this->filterValidFiles($query, $filesInMaster);
				$mappedFiles = $this->mapFileMeta($validFiles);
				return $mappedFiles;
			  } catch (Exception $e) {
				$filesInMaster = $this->flushMasterCache();
				$validFiles = $this->filterValidFiles($query, $filesInMaster);
				$mappedFiles = $this->mapFileMeta($validFiles);
				return $mappedFiles;
			} 
			
			
			
    }

    public function validateFile($request) {
        $rules = array(
            'file' => 'mimes:' . config('hopper.checkin_upload_mimes'),
        );

        $messages = [
            'mimes' => 'Invalid file type or corrupt file',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return $validation->errors();
        } else {
            return true;
        }
    }

    public function uploadToTemporary($file, $newFileName, $disk = 'local') {
        try {
            $upload_success = Storage::disk($disk)->put($this->hopper_temporary_name . $newFileName, $file);
            $filemeta = Storage::disk($disk)->getMetaData($this->hopper_temporary_name . $newFileName);
            $filemeta['storage_disk'] = $disk;
            $filemeta['mime'] = Storage::disk($disk)->mimeType($this->hopper_temporary_name . $newFileName);
//            $this->dispatch(
//                    new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
//            );
            return $filemeta;
        } catch (Exception $e) {
            return $e;
        }
    }
    
	public function updateTemporary($from, $to, $disk = 'local') {
        try {
            $from_exists = Storage::disk($disk)->exists($this->hopper_temporary_name . $from);
			$to_exists = Storage::disk($disk)->exists($this->hopper_temporary_name . $to);
			//If there is a new file name and it exists in temporary and the newfile also exists
			if ($from_exists && !$to_exists) {
				//Update Filename
				$this->renamefile($this->hopper_temporary_name . $from, $this->hopper_temporary_name . $to, $disk);
				return true;
			} elseif ($from_exists && $to_exists) { //If there is a new file name and it exists in temporary and the target exitst too
				// Delete the File in Temporary
				if (Storage::disk($disk)->delete($this->hopper_temporary_name . $to)) {
					//Copy the temporary file over
					$this->renamefile($this->hopper_temporary_name . $from, $this->hopper_temporary_name . $to, $disk);
					return true;
				}
			} else {
				return false;
			}
        } catch (Exception $e) {
            return $e;
        }
    }

    public function uploadToDone($file, $newFileName) {
        $fd = fopen($file->getRealPath(), 'r+');
        $upload_success = Storage::disk('hopper')
                ->put(env('HOPPER_DONE_NAME', 'DONE/') . $newFileName, $fd);
        fclose($fd);
        if ($upload_success) {
            return true;
        }
        return false;
    }

    public function moveTemporaryNewFileToWorking($newFileName, $sourcedisk = 'local', $targetdisk = 'hopper') {
        if (config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            return $hopperDBX->moveTemporaryToWorking($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbWorking = $hopperDBX->moveTemporaryToWorking($newFileName);
            }
            return $this->_moveHopperTemporaryToHopperWorking($newFileName, $sourcedisk, $targetdisk);
        }
    }

    public function _moveHopperTemporaryToHopperWorking($newFileName, $sourcedisk = 'local', $targetdisk = 'hopper') {
        $newFile_exists = Storage::disk($sourcedisk)->exists($this->hopper_temporary_name . $newFileName);
        $newFile_exists_in_working = Storage::disk($targetdisk)->exists($this->hopper_working_name . $newFileName);
        //If there is a new file name and it exists in temporary and not in working
        if ($newFileName && $newFile_exists && !$newFile_exists_in_working) {
            //Move that file out of Temporary and into Working
             if(config('hopper.use_queue', false)){
				 $this->dispatch(
					 new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk)
				 );
			 }else{
				 $this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk);
			 }	
            return $newFileName;
        } elseif ($newFileName && $newFile_exists && $newFile_exists_in_working) { //If there is a new file name and it exists in temporary and in working
            // Delete the File in Working
            Storage::disk($targetdisk)->delete($this->hopper_working_name . $newFileName);

			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk)
				);
			}else{
				$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk);
			}
			
			return $newFileName;
        } else {
            return false;
        }
    }

    public function copyTemporaryNewFileToMaster($newFileName, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            return $hopperDBX->copyTemporaryToMaster($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyTemporaryToMaster($newFileName);
            }
            return $this->_copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete, $sourcedisk, $targetdisk);
        }
		return $this->_copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete, $sourcedisk, $targetdisk);
    }

    public function _copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper') {
        $newFile_exists = Storage::disk($sourcedisk)->exists($this->hopper_temporary_name . $newFileName);
        $newFile_exists_in_master = Storage::disk($targetdisk)->exists($this->hopper_master_name . $newFileName);
		
        //If there is a new file name and it exists in Temporary and not in Master
        if ($newFileName && $newFile_exists && !$newFile_exists_in_master) {
            //Copy the file from Temporary to master
            if(!$delete){
				
				if(config('hopper.use_queue', false)){
					$this->dispatch(
						new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
					);
				}else{
					$this->copyfile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
				}
				
            }else{
				
				if(config('hopper.use_queue', false)){
					$this->dispatch(
						new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
					);
				}else{
					$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
				}
				
            }
            
            return $this->hopper_master_name . $newFileName;
        } elseif ($newFile_exists && $newFile_exists_in_master) {  //If there is a new file name and it exists in Temporary and in Master
            //Delete the file from master	
            if (Storage::disk($targetdisk)->delete($this->hopper_master_name . $newFileName)) {
                //Copy the file from Temporary to Master
                if(!$delete){
					if(config('hopper.use_queue', false)){
						$this->dispatch(
							new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
						);
					}else{
						$this->copyfile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
					}
				}else{
					if(config('hopper.use_queue', false)){
						$this->dispatch(
							new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
						);
					}else{
						$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
					}
				}
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)); 
                return $this->hopper_master_name . $newFileName;
            }
        } else {
            return false;
        }
    }
	
    public function copyTemporaryNewFileToRelics($filename, $uploaded_filename, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper') {
		return $this->_copyHopperTemporaryNewFileToHopperRelics($filename, $uploaded_filename, $delete, $sourcedisk, $targetdisk);
    }

    public function _copyHopperTemporaryNewFileToHopperRelics($filename, $uploaded_filename, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper') {
        $file_exists = Storage::disk($sourcedisk)->exists($this->hopper_temporary_name . $filename);
        $file_exists_in_relics = Storage::disk($targetdisk)->exists($this->hopper_relics_name . $uploaded_filename);
		
        //If there is a new file name and it exists in Temporary and not in Relics
        if ($uploaded_filename && $file_exists && !$file_exists_in_relics) {
            //Copy the file from Temporary to master
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $filename, $this->hopper_relics_name . $uploaded_filename, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_temporary_name . $filename, $this->hopper_relics_name . $uploaded_filename, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_relics_name . $uploaded_filename;
        } elseif ($file_exists && $file_exists_in_relics) {  //If there is a new file name and it exists in Temporary and in Master
            //Delete the file from relics	
            if (Storage::disk($targetdisk)->delete($this->hopper_relics_name . $uploaded_filename)) {
                //Copy the file from Temporary to Relics
                if(config('hopper.use_queue', false)){
					$this->dispatch(
						new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $filename, $this->hopper_relics_name . $uploaded_filename, $sourcedisk, $targetdisk)
					);
				}else{
					$this->copyfile($this->hopper_temporary_name . $filename, $this->hopper_relics_name . $uploaded_filename, $sourcedisk, $targetdisk);
				}
                return $this->hopper_relics_name . $uploaded_filename;
            }
        } else {
            return false;
        }
    }

    public function copyMasterToWorking($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            $dbMaster = $hopperDBX->copyMasterToWorking($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyMasterToWorking($currentMaster, $updateVersionTo);
            }
//            event(new \App\Events\Backend\Hopper\MasterUpdated($oldFilePath, $newFilePath, $uuid = null));
            $newMaster = $this->_copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo, $sourcedisk, $targetdisk);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $master_exists = Storage::disk($sourcedisk)->exists($this->hopper_master_name . $currentMaster);
        $master_exists_in_working = Storage::disk($targetdisk)->exists($this->hopper_working_name . $currentMaster);
        $fileData = [];
        $fileData = $currentMaster;
		\Log::info('MasterHopperToWorkingHopper triggered: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $currentMaster);
        //If we are updating Master Version and the Master Exists 
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            //Check if the new master exists in working
            $newFileName_exists_in_working = Storage::disk($targetdisk)->exists($this->hopper_working_name . $newFileName);
            //If the new master exists in working
            //Delete the master in Working
            if ($newFileName_exists_in_working) {
				Storage::disk($targetdisk)->delete($this->hopper_working_name . $newFileName);
            } 
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_working_name . $newFileName;
        }
        if ($master_exists) { //If the Master exists in Master
            //If the Master file exists in Working
            if ($master_exists_in_working) {
                //Delete the master in Working
				Storage::disk($targetdisk)->delete($this->hopper_working_name . $currentMaster);
            }
			
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_working_name . $currentMaster;
        } else {
            return false;
        }
        return $fileData;
    }

    public function copyMasterToMaster($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            $dbMaster = $hopperDBX->copyMasterToMaster($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyMasterToMaster($currentMaster, $updateVersionTo);
//                debugbar()->info($dbMaster);
            }
            $newMaster = $this->_copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo, $sourcedisk, $targetdisk);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $master_exists = Storage::disk($sourcedisk)->exists($this->hopper_master_name . $currentMaster);
        //If we are updating Master Version and the Master Exists    
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            $renamed_master_exists_in_master = Storage::disk($targetdisk)->exists($this->hopper_master_name . $newFileName);
//			debugbar()->info('Update'); 
            if ($renamed_master_exists_in_master) {			
				Storage::disk($targetdisk)->delete($this->hopper_master_name . $newFileName);
			}
			
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_master_name . $newFileName;
        } else {

            return false;
        }
    }
	
    public function moveMasterToMaster($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            $dbMaster = $hopperDBX->copyMasterToMaster($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyMasterToMaster($currentMaster, $updateVersionTo);
//                debugbar()->info($dbMaster);
            }
            $newMaster = $this->_copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo, $sourcedisk, $targetdisk);
        }
        return $newMaster;
    }

    public function _moveMasterHopperToMasterHopper($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $master_exists = Storage::disk($sourcedisk)->exists($this->hopper_master_name . $currentMaster);
        //If we are updating Master Version and the Master Exists    
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);

            $renamed_master_exists_in_master = Storage::disk($targetdisk)->exists($this->hopper_master_name . $newFileName);
            if ($renamed_master_exists_in_master) {

				Storage::disk($targetdisk)->delete($this->hopper_master_name . $newFileName);

            }
			
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk)
				);
			}else{
				$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_master_name . $newFileName;
        } else {

            return false;
        }
    }
    
    public function copyMasterToArchive($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            $dbMaster = $hopperDBX->copyMasterToArchive($currentMaster, $updateVersionTo);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyMasterToArchive($currentMaster, $updateVersionTo);
//                debugbar()->info($dbMaster);
            }
            $newMaster = $this->_copyMasterHopperToArchiveHopper($currentMaster, $updateVersionTo, $sourcedisk, $targetdisk);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToArchiveHopper($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $master_exists = Storage::disk($sourcedisk)->exists($this->hopper_master_name . $currentMaster);
        $master_exists_in_target = Storage::disk($targetdisk)->exists($this->hopper_archive_name . $currentMaster);
        $fileData = [];
        $fileData = $currentMaster;
		
        //If we are updating Master Version and the Master Exists       
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            //Check if the new master exists in archive
            $newFileName_exists_in_target = Storage::disk($targetdisk)->exists($this->hopper_archive_name . $newFileName);
            //If the new master exists in working
            //Delete the master in Working
            if ($newFileName_exists_in_target) {
				Storage::disk($targetdisk)->delete($this->hopper_archive_name . $newFileName);
            }
			//Copy the Master Over
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName, $sourcedisk, $targetdisk);
			}
				
            return $this->hopper_archive_name . $newFileName;
        }
        if ($master_exists) { //If the Master exists in Master
            //If the Master file exists in Working
            if ($master_exists_in_target) {
                //Delete the master in Archive
                Storage::disk('hopper')->delete($this->hopper_archive_name . $currentMaster);
            }
			
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster, $sourcedisk, $targetdisk)
				);
			}else{
				$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_archive_name . $currentMaster;
        } else {
            return false;
        }
        return $fileData;
    }
 

    
    public function moveMasterToArchive($currentMaster, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        if (config('hopper.master_storage') === 'dropbox' && config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            $dbMaster = $hopperDBX->moveMasterToArchive($currentMaster);
            $newMaster = $currentMaster;
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->moveMasterToArchive($currentMaster);
//                debugbar()->info($dbMaster);
            }
            $newMaster = $this->_moveMasterHopperToArchiveHopper($currentMaster, $sourcedisk, $targetdisk);
        }
        return $newMaster;
    }

    public function _moveMasterHopperToArchiveHopper($currentMaster, $sourcedisk = 'hopper', $targetdisk = 'hopper') {
        $master_exists = Storage::disk($sourcedisk)->exists($this->hopper_master_name . $currentMaster);
        //If the Master Exists    
        if ($master_exists) {
            //Check if master exists in archive
            $master_exists_in_master = Storage::disk($targetdisk)->exists($this->hopper_archive_name . $currentMaster);
//			debugbar()->info('Update'); 
            if ($master_exists_in_master) {
				
				Storage::disk($targetdisk)->delete($this->hopper_archive_name . $currentMaster);
				
            }
			if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster, $sourcedisk, $targetdisk)
				);
			}else{
				$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster, $sourcedisk, $targetdisk);
			}
			
            return $this->hopper_archive_name . $currentMaster;
        } else {

            return false;
        }
    }
    
    
    
    public function purgeDupesToArchive(){
//        $masterFiles = $this->getAllInMaster();
//        
//        $masterFiles = $masterFiles->map(function ($item, $key) {
//            $item = str_replace($this->hopper_master_name, '', $item);
//            $file_array = $this->getFileParts($item);
//            if(is_array($file_array)){
//               $file_array['filename'] = $item;
//                return $file_array; 
//            }
//            return $item;
//        });
//        
//        $masterFiles = $masterFiles->reject(function ($item) {
//            return !is_array($item);
//        });
////        debugbar()->info($masterFiles);
//        $masterFiles = $masterFiles->groupBy('sessionID');
//        $masterFiles = $masterFiles->reject(function ($item) {
//            return $item->count() < 2;
//        });
////        debugbar()->info($masterFiles);
//        foreach($masterFiles as $masterFilesWithDupes){
//             $masterFilesWithDupes->pop();
//             $masterFilesWithDupes = $masterFilesWithDupes->sortBy('version');
//             foreach($masterFilesWithDupes as $dupeFile){
//
//				
//				$this->movefile($this->hopper_master_name . $dupeFile['filename'], $this->hopper_archive_name . $dupeFile['filename']);
//				
//             }
//             
//        }
        
//        debugbar()->info($masterFiles);
        return true;
    }

    public static function getCurrentVersion($currentFileName) {
//        $currentFileParts = pathinfo($currentFileName)['filename'];
        $currentFileParts = pathinfo($currentFileName, PATHINFO_FILENAME);
        $currentFileNameParts = explode('_', $currentFileParts);
        if (empty($currentFileNameParts)) {
            return false;
        }
		//We only care about the end
		$version = collect($currentFileNameParts)->take(-1)->implode('');

        //If there is no file in Master but placeholer LCCNOFILE is there
        if ($version === 'LCCNOFILE') {
            //The Next version is 7
            $currentVersion = 7;
        } else { //Do the usual thing
            $currentVersion = (int) preg_replace("/[^0-9]/", '', $version);
        }
        return str_pad($currentVersion, 2, '0', STR_PAD_LEFT);
    }

    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null) {

		if ($currentFileExtension === null) {
            $currentFileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
        }
        $currentFileNameArray = $this->getFileParts($currentFileName);
		if(empty($currentFileNameArray)){
			return false;
		}
		//Pop the end off
		$currentFileNameArray->pop();
		//Put the New version at the end
		$currentFileNameArray->put('version', config('hopper.version_prefix', 'LCC') . $nextVersion);
		//Merge to new file name
		if($currentFileExtension === 'txt'){
			$currentFileExtension = 'pptx';
		}
		$newFileName = $currentFileNameArray->implode('_') . '.' . $currentFileExtension;		
		
        return $newFileName;
    }
    
    
    /**
     * Gets fileparts mapped.
     *
     * @return array
     */
    public function getFileParts($currentFileParts){

        $fileNameArrayParts = config('hopper.filenameparts');
        $currentFileNameParts = explode('_', pathinfo($currentFileParts, PATHINFO_FILENAME) );
        $currentFileNameArray = [];
        if(count($currentFileNameParts) < 3){
            //Probably a non-session file
            return $currentFileParts;
        }
        
        foreach($fileNameArrayParts as $key => $partname){
            if(array_key_exists($key, $currentFileNameParts)){
                $currentFileNameArray[$partname] = $currentFileNameParts[$key];
            }else{
                $currentFileNameArray[$partname] = null;
            }
        }
        
        return collect($currentFileNameArray);
    }
    
    
    /**
     * Gets all files in Master with pathname removed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllInMaster($sourcedisk = 'hopper'){
        return collect(Cache::remember('hopper_master_files', 1, function() use ($sourcedisk){
			return Storage::disk($sourcedisk)->files($this->hopper_master_name);
		}));
        //return collect(Storage::disk($sourcedisk)->files($this->hopper_master_name));
    }
	
    /**
     * Gets all files in Master with pathname removed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function flushMasterCache($sourcedisk = 'hopper'){
		Cache::forget('hopper_master_files');
    }
    
    /**
     * Filters valid files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function filterValidFiles($query, $collection, $disk = 'hopper'){

        $collection = $collection->filter(function ($item) use ($query) {
				$fileparts = explode('/', $item);
				
				if(isset($fileparts[1])
					&& str_is( $query, head( explode('_', $fileparts[1] ) ) )
					&& in_array(pathinfo($item, PATHINFO_EXTENSION), explode(',', config('hopper.checkin_upload_mimes') ) )
				){
					return true;				
				}
				return false;
			});
        
        
        return $collection;
    }
	
    /**
     * Maps file collection with file data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mapFileMeta($collection, $sourcedisk = 'hopper'){
		
        $collection = $collection
            ->map(function ($item) use ($sourcedisk) {
                $fileparts = explode('/', $item);
				$hopperfileparts = $this->getFileParts($fileparts[1]);
				$currentVersion = $this->getCurrentVersion($fileparts[1]);
                $filemeta = Storage::disk($sourcedisk)->getMetaData($item);
                $filemeta['mime'] = Storage::disk($sourcedisk)->mimeType($item);
                return [
                    'filename' => $fileparts[1],
					'fileparts' => $hopperfileparts,
					'currentVersion' => (int) $currentVersion,
					'nextVersion' => str_pad($currentVersion + 1, 2, '0', STR_PAD_LEFT),
                    'storage_disk' => $sourcedisk,
                    'type' => $filemeta['type'],
                    'filemeta' => $filemeta,
                ];
            });
        
        
        return $collection;
    }

}
