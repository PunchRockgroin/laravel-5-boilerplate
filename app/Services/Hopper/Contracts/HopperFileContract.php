<?php

namespace App\Services\Hopper\Contracts;

Interface HopperFileContract
{

    public function getDriverStoragePath($disk);
    
    public function copyfile($oldFilePath, $newFilePath, $disk);
    
    public function movefile($oldFilePath, $newFilePath, $disk);
    
    public function validateFile($request);
    
    public function uploadToTemporary($file, $newFileName);
    
	public function updateTemporary($from, $to);
    
    public function moveTemporaryNewFileToWorking($newFileName);
    
    public function copyTemporaryNewFileToMaster($newFileName, $delete = false);
    
    public function copyMasterToWorking($currentMaster, $updateVersionTo);
    
    public function copyMasterToMaster($currentMaster, $updateVersionTo);
    
    public function copyMasterToArchive($currentMaster, $updateVersionTo);
    
    public function moveMasterToArchive($currentMaster);
    
    public function renameFileVersion($currentFileName, $nextVersion, $currentFileExtension = null);
    
    public function purgeDupesToArchive();
    
//    public function parseDateTimeforEdit(&$data);
    

}