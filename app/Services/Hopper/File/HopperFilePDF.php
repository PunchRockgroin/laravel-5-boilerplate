<?php

namespace App\Services\Hopper\File;

use App\Services\Hopper\Contracts\HopperFilePDFContract;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
//use Validator;
//use Event;
//use Cache;

//use Illuminate\Support\Facades\Config;
//use Carbon\Carbon as Carbon;
//use Vinkla\Pusher\PusherManager;
//use App\Models\Hopper\EventSession;
//use App\Models\Hopper\Visit;
//use App\Models\Hopper\FileEntity;
//use App\Jobs\Hopper\CopyFile;
//use App\Services\Hopper\Contracts\HopperFileContract;
//use App\Services\Hopper\File\HopperFilePDF;

class HopperFilePDF implements HopperFilePDFContract{
	
	use DispatchesJobs;
	
	protected $storagepath;
    protected $pusher;
	protected $hopperfile;
    public $hopper_temporary_name;
    public $hopper_relics_name;
    public $hopper_working_name;
    public $hopper_master_name;
    public $hopper_archive_name;
    public $hopper_pdfarchive_name;
	public $libreofficepath;
	
	 function __construct() {
        $this->storagepath = config('hopper.local_storage');
		
		$this->hopperfile = app('hopper.file');
		
        $this->hopper_temporary_name = env('HOPPER_TEMPORARY_NAME', 'temporary/');
        $this->hopper_relics_name = env('HOPPER_RELICS_NAME', 'relics/');
        $this->hopper_working_name = env('HOPPER_WORKING_NAME', 'working/');
        $this->hopper_master_name = env('HOPPER_MASTER_NAME', '1_Master/');
        $this->hopper_archive_name = env('HOPPER_ARCHIVE_NAME', 'ZZ_Archive/');
        $this->hopper_pdfarchive_name = env('HOPPER_PDFARCHIVE_NAME', 'pdf/');
		
		$this->libreofficepath = env('HOPPER_LIBREOFFICE_PATH', '/usr/bin/libreoffice');
		
    }
	
	/**
     * Checks if Libreoffice Available.
     * @return string
     */
	public function checkLibreOffice(){
		
		//command -v libreoffice
		$builder = new ProcessBuilder();
		$builder->setPrefix('command');
		$builder->setArguments([
				'-v',
				$this->libreofficepath
		]);
		
		$process = $builder->getProcess();
		
		try {
			$process->mustRun();
			return $process->getOutput();
		} catch (ProcessFailedException $e) {
			\Log::info($e->getMessage());
			return $e->getMessage();
		}
		
		 
	}
	/**
     * Checks if PDF exists in PDF Archive.
     *
	 * @param $file string
     * @return string
     */
	public function makePDFFilename($file){
		$currentFilename = pathinfo($file, PATHINFO_FILENAME);
		$newPDFName = head( explode('_', $currentFilename) ) . '.pdf';
		return $newPDFName;
	}
	/**
     * Checks if PDF exists in PDF Archive.
     *
	 * @param $currentMaster string
	 * @param $sourcedisk string
     * @return boolean
     */
	public function checkIfPDFExists($currentMaster, $sourcedisk = 'hopper'){
		$pdfName = $this->makePDFFilename($currentMaster);
		$exists = Storage::disk($sourcedisk)->exists($this->hopper_pdfarchive_name . $pdfName);
		return $exists;
	}
	
	/**
     * Entry point to PDF creation.
     *
	 * @param $file string
	 * @param $sourcedisk string
	 * @param $targetdisk string
	 * @param $temporarydisk string
	 * 
     * @return string
     */
	public function createPDF($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local'){
		if(config('hopper.use_queue', false)){
			$this->dispatch(
				new \App\Jobs\Hopper\CreatePDF($file, $sourcedisk, $targetdisk, $temporarydisk)
			);
			return true;
		}else{
			
			return $this->generatePDF($file, $sourcedisk, $targetdisk, $temporarydisk);
		}
	}

	/**
     * Generates a PDF from a file.
     *
	 * @param $file string
	 * @param $sourcedisk string
	 * @param $targetdisk string
	 * @param $temporarydisk string
	 * 
     * @return string
     */
    public function generatePDF($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local'){	
        $master_exists = Storage::disk($sourcedisk)->exists($file);
//        //If the Master Exists    
        if ($master_exists) {
			$currentFilename = pathinfo($file, PATHINFO_FILENAME);
			$newPDFName = $this->makePDFFilename($file);			
			
			$temporarystoragepath = $this->hopperfile->getDriverStoragePath($temporarydisk) . $this->hopper_temporary_name;
			$filepath = $this->hopperfile->getDriverStoragePath($sourcedisk) . $file;
						
			$builder = new ProcessBuilder();
			$builder->setPrefix($this->libreofficepath);
			$builder->setArguments([
				'--headless',
				'--invisible',
				'--convert-to',
				'pdf',
				'--outdir',
				$temporarystoragepath,
				$filepath
			]);
			
			$process = $builder->getProcess();		
			$process->setTimeout(300);
			try {
				$process->mustRun();
				$this->hopperfile->movefile(
					$this->hopper_temporary_name . $currentFilename.'.pdf',
					$this->hopper_pdfarchive_name . $newPDFName,
					$temporarydisk,
					$targetdisk
				);
				return $newPDFName;
				
			} catch (ProcessFailedException $e) {
				\Log::error($e->getMessage());
				return false;
			}

        }
		
		return false;
    }
	
	/**
     * Batch Create PDFs in Directory.
     *
	 * @param $currentMaster string
	 * @param $sourcedisk string
	 * @param $targetdisk string
	 * @param $temporarydisk string
	 * 
     * @return string
     */
    public function createPDFBatch($directory, $overwrite = false, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local'){	
//        $master_exists = Storage::disk($sourcedisk)->exists($directory);
//        //If the Master Exists    
//		debugbar()->info($files);
//		$temporarystoragepath = $this->hopperfile->getDriverStoragePath($temporarydisk) . $this->hopper_temporary_name;
//		$filepath = $this->hopperfile->trailingslashit($this->hopperfile->getDriverStoragePath($sourcedisk) . $directory) . '*.{ppt,pptx,pptm}';
//		$command = sprintf('%s --headless --invisible --convert-to pdf --outdir %s %s',
//			ProcessUtils::escapeArgument($this->libreofficepath),
//			$temporarystoragepath,
//			$filepath
//		);
//		
//		debugbar()->info($command);
//		
//		$process = new Process($command);

//		try {
//			$process->mustRun();
//
////			echo $process->getOutput();
//			debugbar()->info($process->getOutput());
//		} catch (ProcessFailedException $e) {
//			debugbar()->info('Error:'.$$e->getMessage());
//		}

		
//		$process->mustRun(function ($type, $buffer) {
//			if (Process::ERR === $type) {
//				debugbar()->info('ERR > '.$buffer);
//			} else {
//				debugbar()->info('OUT > '.$buffer);
//			}
//		});
		
		$files = Storage::disk($sourcedisk)->files($this->hopperfile->trailingslashit($directory));
		
		$filtered = $this->hopperfile->filterValidFileTypes($files, null, ['txt']);
		
		if(!$overwrite){
			$filtered = $filtered->filter(function ($value, $key) {
				return !$this->checkIfPDFExists($value);
			});
		}
		
		$chunks = $filtered->chunk(5);
		
		foreach($chunks as $chunk){
			foreach($chunk as $file){
				$this->createPDF($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local');
			}
		}
		
//		foreach($filtered as $file){
//			$this->createPDF($file, $sourcedisk = 'hopper', $targetdisk = 'hopper', $temporarydisk = 'local');
//		}

		debugbar()->info($chunks);
		
		return false;
    }
	

	/**
     * Moves Created PDFs to their final destination.
     *
	 * @param $oldPath string
	 * @param $newPath string
	 * @param $targetdisk string
	 * @param $temporarydisk string
	 * 
     * @return string
     */
	public function movePDF($oldPath, $newPath, $targetdisk = 'hopper', $temporarydisk = 'local'){
		if(config('hopper.use_queue', false)){
			$this->dispatch(
				new \App\Jobs\Hopper\MoveFile(
					$oldPath,
					$newPath,
					$temporarydisk,
					$targetdisk
				)
			);
		}else{
			$this->hopperfile->movefile(
				$oldPath,
				$newPath,
				$temporarydisk,
				$targetdisk
			);
		}
	}
	
}