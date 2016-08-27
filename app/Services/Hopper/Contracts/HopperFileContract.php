<?php

namespace App\Services\Hopper\Contracts;

Interface HopperFileContract
{

    public function getDriverStoragePath($disk);
    
    public function copyfile($oldFilePath, $newFilePath, $sourcedisk = 'hopper', $targetdisk = 'hopper');
    
    public function movefile($oldFilePath, $newFilePath, $sourcedisk = 'hopper', $targetdisk = 'hopper');
	
    public function renamefile($oldFilePath, $newFilePath, $targetdisk = 'hopper');
	
	public function locate($query);
    
    public function validateFile($request);
    
    public function uploadToTemporary($file, $newFileName, $disk = 'local');
    
	public function updateTemporary($from, $to, $disk = 'local');
    
    public function moveTemporaryNewFileToWorking($newFileName, $sourcedisk = 'local', $targetdisk = 'hopper');
	
	public function copyTemporaryNewFileToRelics($filename, $uploaded_filename, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper');
    
    public function copyTemporaryNewFileToMaster($newFileName, $delete = false, $sourcedisk = 'local', $targetdisk = 'hopper');
    
    public function copyMasterToWorking($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper');
    
    public function copyMasterToMaster($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper');
	
	public function moveMasterToMaster($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper');
    
    public function copyMasterToArchive($currentMaster, $updateVersionTo = false, $sourcedisk = 'hopper', $targetdisk = 'hopper');
	
    public function moveMasterToArchive($currentMaster, $sourcedisk = 'hopper', $targetdisk = 'hopper');
    
    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null) ;
    
    public function purgeDupesToArchive();
	
	public function getFileParts($currentFileParts);
	
	public function getAllInMaster($sourcedisk = 'hopper');
	
	public function flushMasterCache($sourcedisk = 'hopper');
	
	public function filterValidFiles($query, $collection, $disk = 'hopper');
    
	public function mapFileMeta($collection, $sourcedisk = 'hopper');
//    public function parseDateTimeforEdit(&$data);
    

}