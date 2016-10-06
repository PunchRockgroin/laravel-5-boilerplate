<?php

namespace App\Services\Hopper\Contracts;

Interface HopperFilePDFContract
{

    public function checkLibreOffice();
    
    public function makePDFFilename($file);
    
    public function checkIfPDFExists($currentMaster, $sourcedisk = 'hopper');
	
    public function createPDF($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local');   
	
	public function createPDFBatch($directory, $overwrite = false, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local');
	
	public function movePDF($oldPath, $newPath, $targetdisk = 'hopper', $temporarydisk = 'local');

}