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
use App\Services\Hopper\HopperDBX;
//use App\Jobs\Hopper\CopyFile;
use App\Services\Hopper\Contracts\HopperFileContract;

class HopperFile implements HopperFileContract {

    use DispatchesJobs;

    protected $storagepath;
    protected $pusher;
    protected $driver_storage_path;
    public $hopper_temporary_name;
    public $hopper_working_name;
    public $hopper_master_name;
    public $hopper_archive_name;

    function __construct() {


        $this->storagepath = config('hopper.local_storage');
        $this->driver_storage_path = $this->getDriverStoragePath();

        $this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
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

    public function copyfile($oldFilePath, $newFilePath, $disk = 'hopper') {
        $oldFile_exists = Storage::disk($disk)->exists($oldFilePath);
        $newFile_exists = Storage::disk($disk)->exists($newFilePath);
        if (!$oldFile_exists) {
            return false;
        }
        $fd = fopen($this->getDriverStoragePath($disk) . $oldFilePath, "rb");
        if ($newFile_exists && Storage::disk($disk)->delete($newFilePath)) {
            Storage::disk($disk)
                    ->put($newFilePath, $fd);
        } else {
            Storage::disk($disk)
                    ->put($newFilePath, $fd);
        }
        fclose($fd);
		
		\Log::info('Copy File: '.$oldFilePath .' to ' . $newFilePath);
        return $newFilePath;
    }

    public function movefile($oldFilePath, $newFilePath, $disk = 'hopper') {
        if ($this->copyfile($oldFilePath, $newFilePath, $disk)) {
            Storage::disk($disk)->delete($oldFilePath);
        }
    }
	
    public function renamefile($oldFilePath, $newFilePath, $disk = 'hopper') {
			Storage::disk($disk)->move($oldFilePath, $newFilePath);
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

    public function uploadToTemporary($file, $newFileName) {
        try {
            $upload_success = Storage::disk('hopper')->put($this->hopper_temporary_name . $newFileName, $file);
            $filemeta = Storage::disk('hopper')->getMetaData($this->hopper_temporary_name . $newFileName);
            $filemeta['storage_disk'] = 'hopper';
            $filemeta['mime'] = Storage::disk('hopper')->mimeType($this->hopper_temporary_name . $newFileName);
//            $this->dispatch(
//                    new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
//            );
            return $filemeta;
        } catch (Exception $e) {
            return $e;
        }
    }
    
	public function updateTemporary($from, $to) {
        try {
            $from_exists = Storage::disk('hopper')->exists($this->hopper_temporary_name . $from);
			$to_exists = Storage::disk('hopper')->exists($this->hopper_temporary_name . $to);
			//If there is a new file name and it exists in temporary and the newfile also exists
			if ($from_exists && !$to_exists) {
				//Update Filename
				$this->renamefile($this->hopper_temporary_name . $from, $this->hopper_temporary_name . $to);
				return true;
			} elseif ($from_exists && $to_exists) { //If there is a new file name and it exists in temporary and the target exitst too
				// Delete the File in Temporary
				if (Storage::disk('hopper')->delete($this->hopper_temporary_name . $to)) {
					//Copy the temporary file over
					$this->renamefile($this->hopper_temporary_name . $from, $this->hopper_temporary_name . $to);
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

    public function moveTemporaryNewFileToWorking($newFileName) {
        if (config('hopper.working_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            return $hopperDBX->moveTemporaryToWorking($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbWorking = $hopperDBX->moveTemporaryToWorking($newFileName);
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
//            event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_temporary_name. $newFileName, $this->hopper_working_name . $newFileName));
            if(config('hopper.use_queue', false)){
				$this->dispatch(
					new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName)
				);
			}else{
				$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName);
			}	
            return $newFileName;
        } elseif ($newFileName && $newFile_exists && $newFile_exists_in_working) { //If there is a new file name and it exists in temporary and in working
            // Delete the File in Working
            if (Storage::disk('hopper')->delete($this->hopper_working_name . $newFileName)) {
                //Copy the temporary file over to Working
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName)
					);
				}else{
					$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_working_name . $newFileName);
				}
            }
        } else {
            return false;
        }
    }

    public function copyTemporaryNewFileToMaster($newFileName, $delete = false) {
        if (config('hopper.master_storage') === 'dropbox') {
            $hopperDBX = new HopperDBX();
            return $hopperDBX->copyTemporaryToMaster($newFileName);
        } else {
            if (config('hopper.dropbox_copy')) {
                $hopperDBX = new HopperDBX();
                $dbMaster = $hopperDBX->copyTemporaryToMaster($newFileName);
            }
            return $this->_copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete);
        }
		return $this->_copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete);
    }

    public function _copyHopperTemporaryNewFileToHopperMaster($newFileName, $delete = false) {
        $newFile_exists = Storage::disk('hopper')->exists($this->hopper_temporary_name . $newFileName);
        $newFile_exists_in_master = Storage::disk('hopper')->exists($this->hopper_master_name . $newFileName);
		
        //If there is a new file name and it exists in Temporary and not in Master
        if ($newFileName && $newFile_exists && !$newFile_exists_in_master) {
            //Copy the file from Temporary to master
            if(!$delete){
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName);
				}
            }else{
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
					);
				}else{
					$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName);
				}
            }
            
            return $this->hopper_master_name . $newFileName;
        } elseif ($newFile_exists && $newFile_exists_in_master) {  //If there is a new file name and it exists in Temporary and in Master
            //Delete the file from master	
            if (Storage::disk('hopper')->delete($this->hopper_master_name . $newFileName)) {
                //Copy the file from Temporary to Master
                if(!$delete){
					if(config('hopper.use_queue', false)){
						$this->dispatch(
                            new \App\Jobs\Hopper\CopyFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
						);
					}else{
						$this->copyfile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName);
					}
					
                }else{
					if(config('hopper.use_queue', false)){
						$this->dispatch(
                            new \App\Jobs\Hopper\MoveFile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)
						);
					}else{
						$this->movefile($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName);
					}
					

                }
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_temporary_name . $newFileName, $this->hopper_master_name . $newFileName)); 
                return $this->hopper_master_name . $newFileName;
            }
        } else {
            return false;
        }
    }

    public function copyMasterToWorking($currentMaster, $updateVersionTo = false) {
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
            $newMaster = $this->_copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToWorkingHopper($currentMaster, $updateVersionTo = false) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        $master_exists_in_working = Storage::disk('hopper')->exists($this->hopper_working_name . $currentMaster);
        $fileData = [];
        $fileData = $currentMaster;
		\Log::info('MasterHopperToWorkingHopper triggered: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $currentMaster);
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
				\Log::info('MasterHopperToWorkingHopper: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $newFileName);
				if(config('hopper.use_queue', false)){
					$this->dispatch(
						new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName);
				}
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            } else { //If the new master does not exist
                //Copy the Master Over
				\Log::info('MasterHopperToWorkingHopper: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $newFileName);
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $newFileName);
				}
				
//               
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            }
            return $this->hopper_working_name . $newFileName;
        }
        if ($master_exists) { //If the Master exists in Master
            //If the Master file exists in Working
            if ($master_exists_in_working) {
                //Delete the master in Working
                if (Storage::disk('hopper')->delete($this->hopper_working_name . $currentMaster)) {
                    //Copy the master over
					\Log::info('MasterHopperToWorkingHopper: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $currentMaster);
					
					if(config('hopper.use_queue', false)){
						$this->dispatch(
                            new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster)
						);
					}else{
						$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster);
					}
                    //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
                }
            } else {//If the Master file doesnt exist in Working
                //Copy the master over
				\Log::info('MasterHopperToWorkingHopper: '. $this->hopper_master_name . $currentMaster .' to '. $this->hopper_working_name . $currentMaster);
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_working_name . $currentMaster);
				}
				

                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
            }

            return $this->hopper_working_name . $currentMaster;
        } else {
            return false;
        }
        return $fileData;
    }

    public function copyMasterToMaster($currentMaster, $updateVersionTo = false) {
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
            if ($renamed_master_exists_in_master && Storage::disk('hopper')->delete($this->hopper_master_name . $newFileName)) {
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $currentMaster)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $currentMaster);
				}
            } else {
                //Copy the Current Master to a new filename
				//event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_master_name  . $newFileName));
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName);
				}        
            }
            return $this->hopper_master_name . $newFileName;
        } else {

            return false;
        }
    }
	
    public function moveMasterToMaster($currentMaster, $updateVersionTo = false) {
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
            $newMaster = $this->_copyMasterHopperToMasterHopper($currentMaster, $updateVersionTo);
        }
        return $newMaster;
    }

    public function _moveMasterHopperToMasterHopper($currentMaster, $updateVersionTo = false) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        //If we are updating Master Version and the Master Exists    
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);

            $renamed_master_exists_in_master = Storage::disk('hopper')->exists($this->hopper_master_name . $newFileName);
            if ($renamed_master_exists_in_master && Storage::disk('hopper')->delete($this->hopper_master_name . $newFileName)) {
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $currentMaster)
					);
				}else{
					$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName);
				}
				

            } else {
                //Move the Current Master to a new filename
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName)
					);
				}else{
					$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_master_name . $newFileName);
				}
            }
            return $this->hopper_master_name . $newFileName;
        } else {

            return false;
        }
    }
    
    public function copyMasterToArchive($currentMaster, $updateVersionTo = false) {
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
            $newMaster = $this->_copyMasterHopperToArchiveHopper($currentMaster, $updateVersionTo);
        }
        return $newMaster;
    }

    public function _copyMasterHopperToArchiveHopper($currentMaster, $updateVersionTo = false) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        $master_exists_in_target = Storage::disk('hopper')->exists($this->hopper_archive_name . $currentMaster);
        $fileData = [];
        $fileData = $currentMaster;
		
        //If we are updating Master Version and the Master Exists       
        if ($updateVersionTo && $master_exists) {
            //Get a new file name
            $newFileName = $this->renameFileVersion($currentMaster, $updateVersionTo);
            //Check if the new master exists in archive
            $newFileName_exists_in_target = Storage::disk('hopper')->exists($this->hopper_archive_name . $newFileName);
            //If the new master exists in working
            //Delete the master in Working
            if ($newFileName_exists_in_target && Storage::disk('hopper')->delete($this->hopper_archive_name . $newFileName)) {
                //Copy the master over
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName);
				}
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            } else { //If the new master does not exist
                //Copy the Master Over
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $newFileName);
				}
				

                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $newFileName));
            }
            return $this->hopper_archive_name . $newFileName;
        }
        if ($master_exists) { //If the Master exists in Master
            //If the Master file exists in Working
            if ($master_exists_in_target) {
                //Delete the master in Archive
                if (Storage::disk('hopper')->delete($this->hopper_archive_name . $currentMaster)) {
                    //Copy the master over
					if(config('hopper.use_queue', false)){
						$this->dispatch(
                            new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster)
						);
					}else{
						$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster);
					}
                    //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
                }
            } else {//If the Master file doesnt exist in Working
                //Copy the master over
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\CopyFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster)
					);
				}else{
					$this->copyfile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster);
				}
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_working_name . $currentMaster));
            }

            return $this->hopper_archive_name . $currentMaster;
        } else {
            return false;
        }
        return $fileData;
    }


    
    public function moveMasterToArchive($currentMaster) {
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
            $newMaster = $this->_moveMasterHopperToArchiveHopper($currentMaster);
        }
        return $newMaster;
    }

    public function _moveMasterHopperToArchiveHopper($currentMaster) {
        $master_exists = Storage::disk('hopper')->exists($this->hopper_master_name . $currentMaster);
        //If the Master Exists    
        if ($master_exists) {
            //Check if master exists in archive
            $master_exists_in_master = Storage::disk('hopper')->exists($this->hopper_archive_name . $currentMaster);
//			debugbar()->info('Update'); 
            if ($master_exists_in_master && Storage::disk('hopper')->delete($this->hopper_archive_name . $currentMaster)) {
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster)
					);
				}else{
					$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster);
				}
                //event(new \App\Events\Backend\Hopper\MasterUpdated($this->hopper_master_name  . $currentMaster, $this->hopper_archive_name  . $currentMaster));
            } else {
                //Copy the Current Master to archive
				if(config('hopper.use_queue', false)){
					$this->dispatch(
                        new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster)
					);
				}else{
					$this->movefile($this->hopper_master_name . $currentMaster, $this->hopper_archive_name . $currentMaster);
				}
            }
            return $this->hopper_archive_name . $currentMaster;
        } else {

            return false;
        }
    }
    
    
    
    public function purgeDupesToArchive(){
        $masterFiles = $this->getAllInMaster();
        
        $masterFiles = $masterFiles->map(function ($item, $key) {
            $item = str_replace($this->hopper_master_name, '', $item);
            $file_array = $this->getFileParts($item);
            if(is_array($file_array)){
               $file_array['filename'] = $item;
                return $file_array; 
            }
            return $item;
        });
        
        $masterFiles = $masterFiles->reject(function ($item) {
            return !is_array($item);
        });
//        debugbar()->info($masterFiles);
        $masterFiles = $masterFiles->groupBy('sessionID');
        $masterFiles = $masterFiles->reject(function ($item) {
            return $item->count() < 2;
        });
//        debugbar()->info($masterFiles);
        foreach($masterFiles as $masterFilesWithDupes){
             $masterFilesWithDupes->pop();
             $masterFilesWithDupes = $masterFilesWithDupes->sortBy('version');
             foreach($masterFilesWithDupes as $dupeFile){
                $this->dispatch(
                    new \App\Jobs\Hopper\MoveFile($this->hopper_master_name . $dupeFile['filename'], $this->hopper_archive_name . $dupeFile['filename'])
                );
             }
             
        }
        
//        debugbar()->info($masterFiles);
        
    }

    public static function getCurrentVersion($currentFileName) {
        $currentFileParts = pathinfo($currentFileName)['filename'];
        $currentFileNameParts = explode('_', $currentFileParts);
        if (empty($currentFileNameParts)) {
            return false;
        }
        //If there is no file in Master but placeholer LCCNOFILE is there
        if ($currentFileNameParts[3] === 'LCCNOFILE') {
            //The Next version is 7
            $currentVersion = 7;
        } else { //Do the usual thing
            $currentVersion = (int) preg_replace("/[^0-9]/", '', $currentFileNameParts[3]);
        }
        return str_pad($currentVersion, 2, '0', STR_PAD_LEFT);
    }

    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null) {

        $currentFileParts = pathinfo($currentFileName)['filename'];
        if ($currentFileExtension === null) {
            $currentFileExtension = pathinfo($currentFileName)['extension'];
        }
//        
        $currentFileNameArray = $this->getFileParts($currentFileParts);

        $newFileName = $currentFileNameArray['sessionID']
                . '_' . $currentFileNameArray['speaker']
                . '_' . $currentFileNameArray['roomIDs']
                . '_LCC' . $nextVersion
                . (empty($currentFileNameArray['shareStatus']) ? '' : '_' . $currentFileNameArray['shareStatus']) //Sometimes, share status isn't there
                . '.' . $currentFileExtension;

        return $newFileName;
    }
    
    
    /**
     * Gets fileparts mapped.
     *
     * @return array
     */
    public function getFileParts($currentFileParts){
        $fileNameArrayParts = ['sessionID', 'speaker', 'roomIDs', 'version', 'shareStatus'];
        $currentFileNameParts = explode('_', $currentFileParts);
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
        
        
//        $currentFileNameArray = [
//            'sessionID' =>  (empty($currentFileNameParts[0]) ? null : $currentFileNameParts[0]),
//            'speaker' => (empty($currentFileNameParts[1]) ? null : $currentFileNameParts[1]),
//            'roomIDs' => (empty($currentFileNameParts[2]) ? null : $currentFileNameParts[2]),
//            'version' => (empty($currentFileNameParts[3]) ? null : $currentFileNameParts[3]),
//            'shareStatus' => (empty($currentFileNameParts[4]) ? null : $currentFileNameParts[4])
//        ];
        return $currentFileNameArray;
    }
    
    
    /**
     * Gets all files in Master with pathname removed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllInMaster(){
        $collection = collect(Storage::disk('hopper')->files($this->hopper_master_name));
        return $collection;
    }
    
    /**
     * Maps file collection with file data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function mapFileMeta($collection, $disk = 'hopper'){

        $collection = $collection
            ->map(function ($item) use ($disk) {
                $fileparts = explode('/', $item);
                $filemeta = Storage::disk($disk)->getMetaData($item);
                $filemeta['mime'] = Storage::disk($disk)->mimeType($item);
                return [
                    'filename' => $fileparts[1],
                    'storage_disk' => $disk,
                    'type' => $filemeta['type'],
                    'path' => $filemeta['path'],
                    'mime' => $filemeta['mime'],
                    'filemeta' => $filemeta,
                ];
            });
        
        
        return $collection;
    }

}
